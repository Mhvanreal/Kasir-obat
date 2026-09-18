<?php

namespace Tests\Feature;

use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Test ini butuh 4 user + master data (supplier/pelanggan/obat). Seed manual
        // supaya test self-contained walau test lain memakai RefreshDatabase (yang
        // meninggalkan DB kosong setelah selesai).
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    private function admin(): User
    {
        return User::where('role', 'admin')->firstOrFail();
    }

    private function karyawan(): User
    {
        return User::where('role', 'karyawan')->firstOrFail();
    }

    public function test_supplier_page_can_be_rendered(): void
    {
        $this->actingAs($this->admin())->get('/admin/supplier')->assertStatus(200);
    }

    public function test_supplier_can_be_created_with_auto_code(): void
    {
        $this->actingAs($this->admin())->post('/admin/supplier', [
            'nm_supplier' => 'PT Test Sejahtera',
            'kota' => 'Jakarta',
            'telpon' => '021-0000000',
        ])->assertRedirect('/admin/supplier');

        $this->assertDatabaseHas('suppliers', [
            'kd_supplier' => 'SUP004',
            'nm_supplier' => 'PT Test Sejahtera',
        ]);
    }

    public function test_supplier_can_be_updated(): void
    {
        $this->actingAs($this->admin())->put('/admin/supplier/SUP001', [
            'nm_supplier' => 'PT Kimia Farma Updated',
            'kota' => 'Jakarta Pusat',
        ])->assertRedirect('/admin/supplier');

        $this->assertDatabaseHas('suppliers', [
            'kd_supplier' => 'SUP001',
            'nm_supplier' => 'PT Kimia Farma Updated',
        ]);
    }

    public function test_supplier_with_obats_cannot_be_deleted(): void
    {
        $this->actingAs($this->admin())->delete('/admin/supplier/SUP001')
            ->assertRedirect('/admin/supplier')
            ->assertSessionHas('error');

        $this->assertDatabaseHas('suppliers', ['kd_supplier' => 'SUP001']);
    }

    public function test_supplier_without_obats_can_be_deleted(): void
    {
        Supplier::create([
            'kd_supplier' => 'SUPXXX',
            'nm_supplier' => 'Supplier Tanpa Obat',
        ]);

        $this->actingAs($this->admin())->delete('/admin/supplier/SUPXXX')
            ->assertRedirect('/admin/supplier');

        $this->assertDatabaseMissing('suppliers', ['kd_supplier' => 'SUPXXX']);
    }

    public function test_pelanggan_can_be_created_with_auto_code(): void
    {
        $this->actingAs($this->admin())->post('/admin/pelanggan', [
            'nm_pelanggan' => 'Andi Wijaya',
            'telpon' => '081234000111',
        ])->assertRedirect('/admin/pelanggan');

        $this->assertDatabaseHas('pelanggans', [
            'kd_pelanggan' => 'PLG004',
            'nm_pelanggan' => 'Andi Wijaya',
        ]);
    }

    public function test_pelanggan_can_be_updated(): void
    {
        $this->actingAs($this->admin())->put('/admin/pelanggan/PLG001', [
            'nm_pelanggan' => 'Umum Update',
            'kota' => 'Semarang',
        ])->assertRedirect('/admin/pelanggan');

        $this->assertDatabaseHas('pelanggans', [
            'kd_pelanggan' => 'PLG001',
            'nm_pelanggan' => 'Umum Update',
        ]);
    }

    public function test_pelanggan_can_be_deleted_when_no_transactions(): void
    {
        Pelanggan::create([
            'kd_pelanggan' => 'PLGXXX',
            'nm_pelanggan' => 'Pelanggan Tanpa Riwayat',
        ]);

        $this->actingAs($this->admin())->delete('/admin/pelanggan/PLGXXX')
            ->assertRedirect('/admin/pelanggan');

        $this->assertDatabaseMissing('pelanggans', ['kd_pelanggan' => 'PLGXXX']);
    }

    public function test_quick_store_pelanggan_returns_json(): void
    {
        $response = $this->actingAs($this->karyawan())->post('/karyawan/pelanggan', [
            'nm_pelanggan' => 'Pelanggan Cepat',
            'telpon' => '081234567899',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('pelanggan.nm_pelanggan', 'Pelanggan Cepat');

        $this->assertDatabaseHas('pelanggans', [
            'kd_pelanggan' => 'PLG004',
            'nm_pelanggan' => 'Pelanggan Cepat',
        ]);
    }

    public function test_pembelian_increments_stock_and_updates_harga_beli(): void
    {
        $obat = Obat::firstOrFail();
        $stokAwal = $obat->stok;
        $hargaBeliBaru = (float) $obat->harga_beli + 1000;

        $this->actingAs($this->admin())->post('/admin/pembelian', [
            'kd_supplier' => 'SUP001',
            'diskon' => 10,
            'items' => [
                ['kd_obat' => $obat->kd_obat, 'jumlah' => 5, 'harga_beli' => $hargaBeliBaru],
            ],
        ])->assertRedirect('/admin/pembelian');

        $pembelian = Pembelian::latest('created_at')->first();
        $this->assertNotNull($pembelian);
        $this->assertSame(5, (int) PembelianDetail::where('nota', $pembelian->nota)->first()->jumlah);

        $obatSegar = $obat->fresh();
        $this->assertSame($stokAwal + 5, $obatSegar->stok);
        $this->assertEquals($hargaBeliBaru, (float) $obatSegar->harga_beli);
    }

    public function test_karyawan_cannot_store_pembelian(): void
    {
        $this->actingAs($this->karyawan())->post('/admin/pembelian', [
            'kd_supplier' => 'SUP001',
            'items' => [],
        ])->assertStatus(403);
    }
}
