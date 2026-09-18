<?php

namespace Tests\Feature;

use App\Models\Obat;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Supplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Menguji perilaku dashboard yang berbeda per role:
 * - Karyawan → hanya melihat penjualan miliknya sendiri.
 * - Admin & Owner → melihat penjualan agregat seluruh karyawan.
 */
class DashboardScopeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $owner;
    protected User $karyawanA;
    protected User $karyawanB;
    protected User $karyawanC;
    protected Obat $obat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin     = User::factory()->create(['role' => 'admin',    'name' => 'Admin Utama']);
        $this->owner     = User::factory()->create(['role' => 'owner',    'name' => 'Owner Utama']);
        $this->karyawanA = User::factory()->create(['role' => 'karyawan', 'name' => 'Karyawan A']);
        $this->karyawanB = User::factory()->create(['role' => 'karyawan', 'name' => 'Karyawan B']);
        $this->karyawanC = User::factory()->create(['role' => 'karyawan', 'name' => 'Karyawan C']);

        $supplier = Supplier::factory()->create();
        $this->obat = Obat::factory()->create(['harga_jual' => 10000, 'kd_supplier' => $supplier->kd_supplier]);

        // Bikin transaksi: Karyawan A = 1.000.000 (5 trx), Karyawan B = 200.000 (2 trx).
        // Karyawan C tidak punya transaksi.
        for ($i = 0; $i < 5; $i++) {
            $this->buatPenjualan($this->karyawanA, 200_000);
        }
        for ($i = 0; $i < 2; $i++) {
            $this->buatPenjualan($this->karyawanB, 100_000);
        }
    }

    private function buatPenjualan(User $user, int $grandTotal): void
    {
        $nota = 'TRX-' . now()->format('ymdHis') . '-' . strtoupper(substr(uniqid(), -3));
        DB::transaction(function () use ($user, $grandTotal, $nota) {
            Penjualan::create([
                'nota'              => $nota,
                'tgl_nota'          => Carbon::today(),
                'diskon'            => 0,
                'total'             => $grandTotal,
                'grand_total'       => $grandTotal,
                'metode_pembayaran' => 'cash',
                'user_id'           => $user->id,
            ]);
            PenjualanDetail::create([
                'nota'       => $nota,
                'kd_obat'    => $this->obat->kd_obat,
                'jumlah'     => 1,
                'harga_jual' => $grandTotal,
                'subtotal'   => $grandTotal,
            ]);
        });
    }

    // ===== Karyawan → data pribadi =====

    public function test_karyawan_a_melihat_penjualan_miliknya_sendiri_1jt(): void
    {
        $response = $this->actingAs($this->karyawanA)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('penjualanHariIni', 1_000_000);
        $response->assertViewHas('penjualanBulanIni', 1_000_000);
        $response->assertViewHas('transaksiHariIni', 5);
        $response->assertViewHas('isKaryawan', true);
    }

    public function test_karyawan_b_melihat_penjualan_miliknya_sendiri_200rb(): void
    {
        $response = $this->actingAs($this->karyawanB)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('penjualanHariIni', 200_000);
        $response->assertViewHas('penjualanBulanIni', 200_000);
        $response->assertViewHas('transaksiHariIni', 2);
    }

    public function test_karyawan_c_tanpa_transaksi_melihat_nol(): void
    {
        $response = $this->actingAs($this->karyawanC)->get(route('dashboard'));

        $response->assertStatus(200);
        // sum() dari kosong = 0 (int atau string "0" dari MySQL). Bandingkan pakai casting.
        $response->assertViewHas('penjualanHariIni', function ($v) {
            return (int) $v === 0;
        });
        $response->assertViewHas('penjualanBulanIni', function ($v) {
            return (int) $v === 0;
        });
        $response->assertViewHas('transaksiHariIni', 0);
    }

    public function test_karyawan_tidak_melihat_transaksi_karyawan_lain_di_recent_list(): void
    {
        $response = $this->actingAs($this->karyawanA)->get(route('dashboard'));

        $response->assertStatus(200);
        $recent = $response->viewData('recentPenjualans');

        // Semua transaksi di recent list harus milik karyawanA.
        $this->assertGreaterThan(0, $recent->count(), 'Karyawan A punya transaksi, seharusnya recent list tidak kosong');
        foreach ($recent as $trx) {
            $this->assertSame($this->karyawanA->id, $trx->user_id, "Recent transaksi bocor: nota {$trx->nota} milik user_id={$trx->user_id}");
        }
    }

    public function test_karyawan_tidak_melihat_pembelian(): void
    {
        // View untuk karyawan tetap mengirim 'pembelianBulanIni' = 0.
        $response = $this->actingAs($this->karyawanA)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('pembelianBulanIni', function ($v) {
            return (int) $v === 0;
        });
    }

    public function test_karyawan_tidak_melihat_section_penjualan_per_karyawan(): void
    {
        $response = $this->actingAs($this->karyawanA)->get(route('dashboard'));

        $response->assertStatus(200);
        // Section rekap "Penjualan per Karyawan" hanya untuk admin/owner
        $response->assertDontSee('Penjualan per Karyawan');
        // Data juga tidak dikirim ke view (atau dikirim tapi kosong).
        $perKaryawan = $response->viewData('penjualanPerKaryawan');
        $this->assertSame(0, $perKaryawan->count());
    }

    // ===== Admin & Owner → data agregat semua =====

    public function test_admin_melihat_total_semua_karyawan_12jt(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);
        // Total = 1.000.000 (A) + 200.000 (B) = 1.200.000
        $response->assertViewHas('penjualanHariIni', 1_200_000);
        $response->assertViewHas('penjualanBulanIni', 1_200_000);
        $response->assertViewHas('transaksiHariIni', 7);
        $response->assertViewHas('isKaryawan', false);
    }

    public function test_owner_melihat_total_semua_karyawan_12jt(): void
    {
        $response = $this->actingAs($this->owner)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('penjualanHariIni', 1_200_000);
        $response->assertViewHas('penjualanBulanIni', 1_200_000);
    }

    public function test_admin_melihat_breakdown_per_karyawan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $rekap = $response->viewData('penjualanPerKaryawan');

        // Rekap harus mencakup semua user (admin, owner, 3 karyawan) = 5 user
        $this->assertSame(5, $rekap->count());

        // Karyawan A: 1jt, Karyawan B: 200k, Karyawan C: 0, Admin/Owner: 0
        $rekapMap = $rekap->keyBy('id');
        $this->assertSame(1_000_000.0, (float) $rekapMap[$this->karyawanA->id]->total_penjualan);
        $this->assertSame(200_000.0, (float) $rekapMap[$this->karyawanB->id]->total_penjualan);
        $this->assertSame(0.0, (float) $rekapMap[$this->karyawanC->id]->total_penjualan);
        $this->assertSame(5, (int) $rekapMap[$this->karyawanA->id]->jumlah_transaksi);
        $this->assertSame(2, (int) $rekapMap[$this->karyawanB->id]->jumlah_transaksi);
        $this->assertSame(0, (int) $rekapMap[$this->karyawanC->id]->jumlah_transaksi);
    }

    public function test_admin_melihat_section_penjualan_per_karyawan_di_html(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Penjualan per Karyawan');
        $response->assertSee('Karyawan A');
        $response->assertSee('Karyawan B');
        $response->assertSee('Karyawan C');
    }

    // ===== Chart trend juga di-scope =====

    public function test_karyawan_chart_trend_hanya_dari_penjualan_sendiri(): void
    {
        $response = $this->actingAs($this->karyawanA)->get(route('dashboard'));

        $response->assertStatus(200);
        $trend = $response->viewData('trendPenjualan');

        // Total di trendPenjualan harus sama dengan penjualan karyawanA (1jt), bukan total gabungan.
        $sum = array_sum($trend);
        $this->assertEqualsWithDelta(1_000_000, $sum, 0.01);
    }

    public function test_admin_chart_trend_dari_semua_penjualan(): void
    {
        $response = $this->actingAs($this->admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $trend = $response->viewData('trendPenjualan');

        $sum = array_sum($trend);
        $this->assertEqualsWithDelta(1_200_000, $sum, 0.01);
    }
}
