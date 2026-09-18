<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransaksiPenjualanTest extends TestCase
{
    use RefreshDatabase;

    protected User $karyawan;
    protected Obat $obat;
    protected Pelanggan $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip CSRF saja supaya auth + role middleware tetap aktif untuk test
        // guest_tidak_dapat_mengakses_halaman_transaksi.
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Create test user dengan role karyawan
        $this->karyawan = User::factory()->create([
            'role' => 'karyawan',
        ]);

        // Create test obat dengan stok
        $this->obat = Obat::factory()->create([
            'stok' => 50,
            'harga_jual' => 10000,
        ]);

        // Create test pelanggan
        $this->pelanggan = Pelanggan::factory()->create();
    }

    /** @test */
    public function karyawan_dapat_mengakses_halaman_transaksi()
    {
        $response = $this->actingAs($this->karyawan)
            ->get(route('karyawan.transaksi'));

        $response->assertStatus(200);
        $response->assertViewIs('karyawan.transaksi');
    }

    /** @test */
    public function guest_tidak_dapat_mengakses_halaman_transaksi()
    {
        $response = $this->get(route('karyawan.transaksi'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function dapat_membuat_transaksi_penjualan_dengan_cash()
    {
        $response = $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => 0,
                'metode_pembayaran' => 'cash',
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'qty' => 5,
                    ]
                ],
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify transaksi tersimpan di database
        $this->assertDatabaseHas('penjualans', [
            'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
            'user_id' => $this->karyawan->id,
            'metode_pembayaran' => 'cash',
            'diskon' => 0,
            'total' => 50000, // 5 * 10000
            'grand_total' => 50000,
        ]);

        // Verify detail transaksi
        $penjualan = Penjualan::first();
        $this->assertDatabaseHas('penjualan_details', [
            'nota' => $penjualan->nota,
            'kd_obat' => $this->obat->kd_obat,
            'jumlah' => 5,
            'harga_jual' => 10000,
        ]);

        // Verify stok berkurang
        $this->obat->refresh();
        $this->assertEquals(45, $this->obat->stok);
    }

    /** @test */
    public function dapat_membuat_transaksi_penjualan_dengan_qris()
    {
        $response = $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => 10,
                'metode_pembayaran' => 'qris',
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'qty' => 3,
                    ]
                ],
            ]);

        $response->assertSessionHasNoErrors();

        $penjualan = Penjualan::first();
        
        $this->assertEquals('qris', $penjualan->metode_pembayaran);
        $this->assertEquals(10, $penjualan->diskon);
        $this->assertEquals(30000, $penjualan->total); // 3 * 10000
        $this->assertEquals(27000, $penjualan->grand_total); // 30000 - (10% * 30000)
    }

    /** @test */
    public function dapat_membuat_transaksi_tanpa_pelanggan()
    {
        $response = $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => null,
                'diskon' => 0,
                'metode_pembayaran' => 'cash',
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'qty' => 2,
                    ]
                ],
            ]);

        $response->assertSessionHasNoErrors();

        $penjualan = Penjualan::first();
        $this->assertNull($penjualan->kd_pelanggan);
    }

    /** @test */
    public function dapat_membuat_transaksi_dengan_multiple_items()
    {
        $obat2 = Obat::factory()->create([
            'stok' => 30,
            'harga_jual' => 15000,
        ]);

        $response = $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => 0,
                'metode_pembayaran' => 'cash',
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'qty' => 2,
                    ],
                    [
                        'kd_obat' => $obat2->kd_obat,
                        'qty' => 3,
                    ]
                ],
            ]);

        $response->assertSessionHasNoErrors();

        $penjualan = Penjualan::first();
        $this->assertEquals(2, $penjualan->details()->count());
        $this->assertEquals(65000, $penjualan->total); // (2*10000) + (3*15000)

        // Verify stok berkurang untuk semua item
        $this->obat->refresh();
        $obat2->refresh();
        $this->assertEquals(48, $this->obat->stok);
        $this->assertEquals(27, $obat2->stok);
    }

    /** @test */
    public function validasi_gagal_jika_items_kosong()
    {
        $response = $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => 0,
                'metode_pembayaran' => 'cash',
                'items' => [],
            ]);

        $response->assertSessionHasErrors(['items']);
    }

    /** @test */
    public function validasi_gagal_jika_metode_pembayaran_tidak_valid()
    {
        $response = $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => 0,
                'metode_pembayaran' => 'credit_card', // Invalid
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'qty' => 2,
                    ]
                ],
            ]);

        $response->assertSessionHasErrors(['metode_pembayaran']);
    }

    /** @test */
    public function validasi_gagal_jika_stok_tidak_cukup()
    {
        $obatLowStock = Obat::factory()->create([
            'stok' => 3,
        ]);

        $response = $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => 0,
                'metode_pembayaran' => 'cash',
                'items' => [
                    [
                        'kd_obat' => $obatLowStock->kd_obat,
                        'qty' => 5, // Melebihi stok
                    ]
                ],
            ]);

        // Controller melempar Exception di dalam DB transaction dan menangkap-nya
        // dengan `back()->with('error', ...)`, bukan validation bag. Jadi cek flash 'error'.
        $response->assertSessionHas('error');

        // Verify stok tidak berubah (transaction di-rollback)
        $obatLowStock->refresh();
        $this->assertEquals(3, $obatLowStock->stok);
    }

    /** @test */
    public function validasi_gagal_jika_obat_tidak_ditemukan()
    {
        $response = $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => 0,
                'metode_pembayaran' => 'cash',
                'items' => [
                    [
                        'kd_obat' => 'OBT-999', // Tidak ada
                        'qty' => 2,
                    ]
                ],
            ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function validasi_gagal_jika_diskon_negatif()
    {
        $response = $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => -10,
                'metode_pembayaran' => 'cash',
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'qty' => 2,
                    ]
                ],
            ]);

        $response->assertSessionHasErrors(['diskon']);
    }

    /** @test */
    public function validasi_gagal_jika_diskon_lebih_dari_100()
    {
        $response = $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => 150,
                'metode_pembayaran' => 'cash',
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'qty' => 2,
                    ]
                ],
            ]);

        $response->assertSessionHasErrors(['diskon']);
    }

    /** @test */
    public function nota_transaksi_di_generate_otomatis()
    {
        $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => 0,
                'metode_pembayaran' => 'cash',
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'qty' => 1,
                    ]
                ],
            ]);

        $penjualan = Penjualan::first();
        
        // Verify format nota: TRX-ymdHis-XXX (XXX = 3 char hex uppercase dari uniqid())
        $this->assertMatchesRegularExpression('/^TRX-\d{12}-[0-9A-F]{3}$/', $penjualan->nota);
        $this->assertTrue(strlen($penjualan->nota) <= 20); // Max length check
    }

    /** @test */
    public function transaksi_menggunakan_harga_jual_saat_transaksi_bukan_harga_saat_ini()
    {
        // Buat transaksi dengan harga awal
        $this->actingAs($this->karyawan)
            ->post(route('karyawan.transaksi.store'), [
                'kd_pelanggan' => $this->pelanggan->kd_pelanggan,
                'diskon' => 0,
                'metode_pembayaran' => 'cash',
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'qty' => 1,
                    ]
                ],
            ]);

        $penjualan = Penjualan::first();
        $detail = $penjualan->details()->first();

        // Verify harga tersimpan di detail (bukan reference ke obat)
        $this->assertEquals(10000, $detail->harga_jual);

        // Update harga obat
        $this->obat->update(['harga_jual' => 15000]);

        // Verify detail tetap pakai harga lama
        $detail->refresh();
        $this->assertEquals(10000, $detail->harga_jual);
    }
}
