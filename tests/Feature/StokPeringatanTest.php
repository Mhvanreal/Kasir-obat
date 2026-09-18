<?php

namespace Tests\Feature;

use App\Models\Obat;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji sistem peringatan stok:
 * - stok <= 0  → habis (merah pekat, notif)
 * - stok < 10  → kritis (merah, notif)
 * - stok 10-20 → rendah (kuning, warning)
 * - stok > 20  → aman (hijau)
 */
class StokPeringatanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->supplier = Supplier::factory()->create();
    }

    // ===== Model classification =====

    public function test_status_stok_habis_untuk_stok_nol_atau_negatif(): void
    {
        $obat = Obat::factory()->create(['stok' => 0, 'kd_supplier' => $this->supplier->kd_supplier]);
        $this->assertSame('habis', $obat->status_stok);
        $this->assertTrue($obat->isStokHabis());
        $this->assertFalse($obat->isStokKritis());
        $this->assertFalse($obat->isStokRendah());
    }

    public function test_status_stok_kritis_untuk_stok_1_sampai_9(): void
    {
        foreach ([1, 5, 9] as $stok) {
            $obat = Obat::factory()->create(['stok' => $stok, 'kd_supplier' => $this->supplier->kd_supplier]);
            $this->assertSame('kritis', $obat->status_stok, "Stok {$stok} harus kritis");
            $this->assertTrue($obat->isStokKritis());
            $this->assertFalse($obat->isStokRendah());
            $this->assertFalse($obat->isStokHabis());
        }
    }

    public function test_status_stok_rendah_untuk_stok_10_sampai_20(): void
    {
        foreach ([10, 15, 20] as $stok) {
            $obat = Obat::factory()->create(['stok' => $stok, 'kd_supplier' => $this->supplier->kd_supplier]);
            $this->assertSame('rendah', $obat->status_stok, "Stok {$stok} harus rendah");
            $this->assertTrue($obat->isStokRendah());
            $this->assertFalse($obat->isStokKritis());
            $this->assertFalse($obat->isStokHabis());
        }
    }

    public function test_status_stok_aman_untuk_stok_diatas_20(): void
    {
        foreach ([21, 50, 100] as $stok) {
            $obat = Obat::factory()->create(['stok' => $stok, 'kd_supplier' => $this->supplier->kd_supplier]);
            $this->assertSame('aman', $obat->status_stok, "Stok {$stok} harus aman");
            $this->assertFalse($obat->isStokRendah());
            $this->assertFalse($obat->isStokKritis());
            $this->assertFalse($obat->isStokHabis());
        }
    }

    // ===== Query scopes =====

    public function test_scope_stok_habis_hanya_mengambil_stok_nol_atau_negatif(): void
    {
        Obat::factory()->create(['stok' => 0, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 5, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 15, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 50, 'kd_supplier' => $this->supplier->kd_supplier]);

        $this->assertSame(1, Obat::stokHabis()->count());
    }

    public function test_scope_stok_kritis_hanya_mengambil_1_sampai_9(): void
    {
        Obat::factory()->create(['stok' => 0, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 1, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 9, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 10, 'kd_supplier' => $this->supplier->kd_supplier]);

        $this->assertSame(2, Obat::stokKritis()->count());
    }

    public function test_scope_stok_rendah_hanya_mengambil_10_sampai_20(): void
    {
        Obat::factory()->create(['stok' => 9, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 10, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 15, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 20, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 21, 'kd_supplier' => $this->supplier->kd_supplier]);

        $this->assertSame(3, Obat::stokRendah()->count());
    }

    public function test_scope_butuh_perhatian_mengambil_semua_dibawah_ambang(): void
    {
        Obat::factory()->create(['stok' => 0, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 5, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 20, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 21, 'kd_supplier' => $this->supplier->kd_supplier]);

        $this->assertSame(3, Obat::butuhPerhatian()->count());
    }

    // ===== Ambang batas konstanta =====

    public function test_ambang_batas_konstanta_10_dan_20(): void
    {
        $this->assertSame(10, Obat::AMBANG_KRITIS);
        $this->assertSame(20, Obat::AMBANG_RENDAH);
    }

    // ===== View integration =====

    public function test_index_obat_menampilkan_banner_notifikasi_jika_ada_stok_kritis(): void
    {
        Obat::factory()->create(['stok' => 3, 'nm_obat' => 'Paracetamol Kritis', 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 50, 'kd_supplier' => $this->supplier->kd_supplier]);

        $response = $this->actingAs($this->admin)->get(route('admin.obat.index'));

        $response->assertStatus(200);
        $response->assertSeeText('Perhatian Stok');
        $response->assertSeeText('kritis');
    }

    public function test_index_obat_tidak_menampilkan_banner_jika_semua_stok_aman(): void
    {
        Obat::factory()->create(['stok' => 100, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 50, 'kd_supplier' => $this->supplier->kd_supplier]);

        $response = $this->actingAs($this->admin)->get(route('admin.obat.index'));

        $response->assertStatus(200);
        $response->assertDontSeeText('Perhatian Stok:'); // banner-level text
    }

    public function test_index_obat_membedakan_hitungan_kritis_dan_rendah(): void
    {
        Obat::factory()->count(2)->create(['stok' => 5, 'kd_supplier' => $this->supplier->kd_supplier]);  // kritis
        Obat::factory()->count(3)->create(['stok' => 15, 'kd_supplier' => $this->supplier->kd_supplier]); // rendah
        Obat::factory()->create(['stok' => 0, 'kd_supplier' => $this->supplier->kd_supplier]);            // habis
        Obat::factory()->create(['stok' => 100, 'kd_supplier' => $this->supplier->kd_supplier]);          // aman

        $response = $this->actingAs($this->admin)->get(route('admin.obat.index'));

        $response->assertStatus(200);
        // 1 habis, 2 kritis, 3 rendah muncul di banner
        $response->assertSee('1</strong> habis', false);
        $response->assertSee('2</strong> kritis', false);
        $response->assertSee('3</strong> rendah', false);
    }

    public function test_badge_kritis_dan_habis_ditandai_dengan_warna_merah(): void
    {
        Obat::factory()->create(['stok' => 5, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 0, 'kd_supplier' => $this->supplier->kd_supplier]);

        $response = $this->actingAs($this->admin)->get(route('admin.obat.index'));

        $response->assertStatus(200);
        // Badge kritis pakai bg-red-100
        $response->assertSee('bg-red-100 text-red-800', false);
        // Badge habis pakai bg-red-600
        $response->assertSee('bg-red-600 text-white', false);
    }

    public function test_badge_rendah_ditandai_dengan_warna_kuning(): void
    {
        Obat::factory()->create(['stok' => 15, 'kd_supplier' => $this->supplier->kd_supplier]);

        $response = $this->actingAs($this->admin)->get(route('admin.obat.index'));

        $response->assertStatus(200);
        $response->assertSee('bg-yellow-100 text-yellow-800', false);
    }

    public function test_dashboard_menghitung_stok_habis_kritis_rendah_terpisah(): void
    {
        Obat::factory()->create(['stok' => 0, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->count(2)->create(['stok' => 5, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->count(3)->create(['stok' => 15, 'kd_supplier' => $this->supplier->kd_supplier]);
        Obat::factory()->create(['stok' => 100, 'kd_supplier' => $this->supplier->kd_supplier]);

        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('obatHabis', 1);
        $response->assertViewHas('obatStokKritis', 2);
        $response->assertViewHas('obatStokRendah', 3);
    }
}
