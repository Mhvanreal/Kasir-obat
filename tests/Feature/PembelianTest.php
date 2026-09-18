<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Obat;
use App\Models\Supplier;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembelianTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Supplier $supplier;
    protected Obat $obat;

    protected function setUp(): void
    {
        parent::setUp();

        // Nonaktifkan hanya CSRF supaya middleware auth + role tetap aktif
        // (test karyawan_tidak_dapat_mengakses_halaman_pembelian butuh CheckRole hidup).
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        // Create test admin user
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create test supplier
        $this->supplier = Supplier::factory()->create();

        // Create test obat with initial stock
        $this->obat = Obat::factory()->create([
            'kd_supplier' => $this->supplier->kd_supplier,
            'stok' => 10,
            'harga_beli' => 5000,
        ]);
    }

    /** @test */
    public function admin_dapat_mengakses_halaman_pembelian()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.pembelian'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pembelian');
    }

    /** @test */
    public function owner_dapat_mengakses_halaman_pembelian()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)
            ->get(route('admin.pembelian'));

        $response->assertStatus(200);
    }

    /** @test */
    public function karyawan_tidak_dapat_mengakses_halaman_pembelian()
    {
        $karyawan = User::factory()->create(['role' => 'karyawan']);

        $response = $this->actingAs($karyawan)
            ->get(route('admin.pembelian'));

        $response->assertStatus(403);
    }

    /** @test */
    public function dapat_membuat_pembelian_restok()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'jumlah' => 20,
                        'harga_beli' => 5500,
                    ]
                ],
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify pembelian tersimpan
        $this->assertDatabaseHas('pembelians', [
            'kd_supplier' => $this->supplier->kd_supplier,
            'user_id' => $this->admin->id,
        ]);

        // Verify detail pembelian
        $pembelian = Pembelian::first();
        $this->assertDatabaseHas('pembelian_details', [
            'nota' => $pembelian->nota,
            'kd_obat' => $this->obat->kd_obat,
            'jumlah' => 20,
            'harga_beli' => 5500,
        ]);

        // Verify stok bertambah
        $this->obat->refresh();
        $this->assertEquals(30, $this->obat->stok); // 10 + 20
    }

    /** @test */
    public function pembelian_update_harga_beli_di_master_obat()
    {
        $initialHargaBeli = $this->obat->harga_beli;

        $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'jumlah' => 10,
                        'harga_beli' => 6000, // Harga beli baru lebih tinggi
                    ]
                ],
            ]);

        // Verify harga beli di master obat terupdate
        $this->obat->refresh();
        $this->assertEquals(6000, $this->obat->harga_beli);
        $this->assertNotEquals($initialHargaBeli, $this->obat->harga_beli);
    }

    /** @test */
    public function dapat_membuat_pembelian_dengan_multiple_items()
    {
        $obat2 = Obat::factory()->create([
            'kd_supplier' => $this->supplier->kd_supplier,
            'stok' => 5,
            'harga_beli' => 8000,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'jumlah' => 15,
                        'harga_beli' => 5200,
                    ],
                    [
                        'kd_obat' => $obat2->kd_obat,
                        'jumlah' => 25,
                        'harga_beli' => 8500,
                    ]
                ],
            ]);

        $response->assertSessionHasNoErrors();

        $pembelian = Pembelian::first();
        $this->assertEquals(2, $pembelian->details()->count());

        // Verify stok bertambah untuk semua item
        $this->obat->refresh();
        $obat2->refresh();
        $this->assertEquals(25, $this->obat->stok); // 10 + 15
        $this->assertEquals(30, $obat2->stok); // 5 + 25
    }

    /** @test */
    public function validasi_gagal_jika_supplier_tidak_valid()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => 'SUP-999', // Tidak ada
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'jumlah' => 10,
                        'harga_beli' => 5000,
                    ]
                ],
            ]);

        $response->assertSessionHasErrors(['kd_supplier']);
    }

    /** @test */
    public function validasi_gagal_jika_items_kosong()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [],
            ]);

        $response->assertSessionHasErrors(['items']);
    }

    /** @test */
    public function validasi_gagal_jika_jumlah_negatif()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'jumlah' => -5, // Negatif
                        'harga_beli' => 5000,
                    ]
                ],
            ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function validasi_gagal_jika_harga_beli_negatif()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'jumlah' => 10,
                        'harga_beli' => -1000, // Negatif
                    ]
                ],
            ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function validasi_gagal_jika_obat_tidak_ditemukan()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [
                    [
                        'kd_obat' => 'OBT-999', // Tidak ada
                        'jumlah' => 10,
                        'harga_beli' => 5000,
                    ]
                ],
            ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function nota_pembelian_di_generate_otomatis()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'jumlah' => 10,
                        'harga_beli' => 5000,
                    ]
                ],
            ]);

        $pembelian = Pembelian::first();
        
        // Verify format nota: PBL-ymdHis-XXX (XXX = 3 char hex uppercase dari uniqid())
        $this->assertMatchesRegularExpression('/^PBL-\d{12}-[0-9A-F]{3}$/', $pembelian->nota);
        $this->assertTrue(strlen($pembelian->nota) <= 20); // Max length check
    }

    /** @test */
    public function pembelian_menggunakan_harga_beli_saat_transaksi()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'jumlah' => 10,
                        'harga_beli' => 5500,
                    ]
                ],
            ]);

        $pembelian = Pembelian::first();
        $detail = $pembelian->details()->first();

        // Verify harga tersimpan di detail
        $this->assertEquals(5500, $detail->harga_beli);

        // Update harga di master obat (simulasi pembelian selanjutnya)
        $this->obat->update(['harga_beli' => 6000]);

        // Verify detail tetap pakai harga lama
        $detail->refresh();
        $this->assertEquals(5500, $detail->harga_beli);
    }

    /** @test */
    public function total_pembelian_dihitung_dengan_benar()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'jumlah' => 10,
                        'harga_beli' => 5000,
                    ]
                ],
            ]);

        $pembelian = Pembelian::first();
        
        $this->assertEquals(50000, $pembelian->total); // 10 * 5000
    }

    /** @test */
    public function pembelian_dari_supplier_yang_berbeda_dengan_obat()
    {
        $supplierLain = Supplier::factory()->create();

        $response = $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $supplierLain->kd_supplier, // Supplier berbeda
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat, // Obat punya supplier lain
                        'jumlah' => 10,
                        'harga_beli' => 5000,
                    ]
                ],
            ]);

        // Should still work (bisa beli dari supplier manapun)
        $response->assertSessionHasNoErrors();
        
        $pembelian = Pembelian::first();
        $this->assertEquals($supplierLain->kd_supplier, $pembelian->kd_supplier);
    }

    /** @test */
    public function dapat_melihat_riwayat_pembelian_di_halaman_pembelian()
    {
        // Create some pembelian records
        $this->actingAs($this->admin)
            ->post(route('admin.pembelian.store'), [
                'kd_supplier' => $this->supplier->kd_supplier,
                'items' => [
                    [
                        'kd_obat' => $this->obat->kd_obat,
                        'jumlah' => 10,
                        'harga_beli' => 5000,
                    ]
                ],
            ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.pembelian'));

        $response->assertStatus(200);
        $response->assertViewHas('pembelians');
    }
}
