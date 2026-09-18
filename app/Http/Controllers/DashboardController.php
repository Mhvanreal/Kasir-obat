<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Preset rentang waktu untuk chart trend di dashboard.
     * key: query param ?range=... | label: dipakai di UI | days: jumlah hari termasuk hari ini
     */
    private const RANGE_PRESETS = [
        'today' => ['label' => 'Hari Ini', 'days' => 1],
        '7d'    => ['label' => '7 Hari Terakhir', 'days' => 7],
        '30d'   => ['label' => '30 Hari Terakhir', 'days' => 30],
    ];

    public function index(Request $request)
    {
        $rangeKey = $request->query('range', '7d');
        if (! array_key_exists($rangeKey, self::RANGE_PRESETS)) {
            $rangeKey = '7d';
        }
        $days = self::RANGE_PRESETS[$rangeKey]['days'];

        $today = Carbon::today();
        $startDate = $today->copy()->subDays($days - 1);
        $monthStart = $today->copy()->startOfMonth();

        /** @var User $user */
        $user = $request->user();

        // Karyawan hanya melihat penjualannya sendiri; admin & owner melihat semua.
        $isKaryawan = $user->isKaryawan();
        $scopedBy = $isKaryawan ? $user : null;

        // ==== KPI: hari ini ====
        $penjualanHariIni = $this->scopedPenjualan($scopedBy)
            ->whereDate('tgl_nota', $today)->sum('grand_total');
        $transaksiHariIni = $this->scopedPenjualan($scopedBy)
            ->whereDate('tgl_nota', $today)->count();

        // ==== KPI: bulan ini ====
        $penjualanBulanIni = $this->scopedPenjualan($scopedBy)
            ->whereBetween('tgl_nota', [$monthStart, $today])->sum('grand_total');

        // Pembelian bulan ini hanya relevan untuk admin/owner (KPI ini disembunyikan di view untuk karyawan).
        $pembelianBulanIni = $isKaryawan
            ? 0
            : Pembelian::whereBetween('tgl_nota', [$monthStart, $today])->sum('grand_total');

        // ==== KPI: stok (global, tidak bergantung user) ====
        $obatHabis = Obat::stokHabis()->count();
        $obatStokKritis = Obat::stokKritis()->count();
        $obatStokRendah = Obat::stokRendah()->count();

        // ==== Chart trend penjualan (line) ====
        $rows = $this->scopedPenjualan($scopedBy)
            ->selectRaw('DATE(tgl_nota) as tanggal, SUM(grand_total) as total, COUNT(*) as jumlah')
            ->whereDate('tgl_nota', '>=', $startDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $trendLabels = [];
        $trendPenjualan = [];
        $trendTransaksi = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $key = $date->toDateString();
            $trendLabels[] = $days <= 1 ? $date->format('H:00') : $date->format('d M');
            $trendPenjualan[] = (float) ($rows[$key]->total ?? 0);
            $trendTransaksi[] = (int) ($rows[$key]->jumlah ?? 0);
        }

        // Untuk preset "Hari Ini" ganti agregasi jadi per-jam (lebih informatif).
        if ($rangeKey === 'today') {
            $perJam = $this->scopedPenjualan($scopedBy)
                ->selectRaw('HOUR(created_at) as jam, SUM(grand_total) as total, COUNT(*) as jumlah')
                ->whereDate('tgl_nota', $today)
                ->groupBy('jam')
                ->orderBy('jam')
                ->get()
                ->keyBy('jam');

            $trendLabels = [];
            $trendPenjualan = [];
            $trendTransaksi = [];
            for ($h = 0; $h < 24; $h++) {
                $trendLabels[] = sprintf('%02d:00', $h);
                $trendPenjualan[] = (float) ($perJam[$h]->total ?? 0);
                $trendTransaksi[] = (int) ($perJam[$h]->jumlah ?? 0);
            }
        }

        // ==== Top 5 obat terlaris (bulan ini, scoped) ====
        $topObats = PenjualanDetail::selectRaw('kd_obat, SUM(jumlah) as total_terjual, SUM(subtotal) as total_pendapatan')
            ->whereHas('penjualan', function ($q) use ($monthStart, $today, $scopedBy) {
                $q->whereBetween('tgl_nota', [$monthStart, $today]);
                if ($scopedBy) {
                    $q->where('user_id', $scopedBy->id);
                }
            })
            ->groupBy('kd_obat')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->with('obat:kd_obat,nm_obat,satuan')
            ->get();

        // ==== Transaksi terbaru (scoped) ====
        $recentPenjualans = $this->scopedPenjualan($scopedBy)
            ->with('pelanggan', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // ==== Distribusi metode pembayaran (bulan ini, scoped) ====
        $metodeBreakdown = $this->scopedPenjualan($scopedBy)
            ->selectRaw('metode_pembayaran, COUNT(*) as jumlah, SUM(grand_total) as total')
            ->whereBetween('tgl_nota', [$monthStart, $today])
            ->groupBy('metode_pembayaran')
            ->get();

        // ==== Breakdown penjualan per karyawan (admin/owner only) ====
        // Menampilkan siapa saja karyawan yang aktif dan berapa penjualan mereka bulan ini.
        $penjualanPerKaryawan = collect();
        if (! $isKaryawan) {
            $penjualanPerKaryawan = User::query()
                ->whereIn('role', ['karyawan', 'admin', 'owner'])
                ->leftJoin('penjualans', function ($join) use ($monthStart, $today) {
                    $join->on('users.id', '=', 'penjualans.user_id')
                        ->whereBetween('penjualans.tgl_nota', [$monthStart, $today]);
                })
                ->select('users.id', 'users.name', 'users.role')
                ->selectRaw('COALESCE(SUM(penjualans.grand_total), 0) as total_penjualan')
                ->selectRaw('COUNT(penjualans.nota) as jumlah_transaksi')
                ->groupBy('users.id', 'users.name', 'users.role')
                ->orderByDesc('total_penjualan')
                ->get();
        }

        return view('dashboard', [
            'rangeKey'             => $rangeKey,
            'rangePresets'         => self::RANGE_PRESETS,
            'rangeLabel'           => self::RANGE_PRESETS[$rangeKey]['label'],
            'isKaryawan'           => $isKaryawan,
            'penjualanHariIni'     => $penjualanHariIni,
            'transaksiHariIni'     => $transaksiHariIni,
            'penjualanBulanIni'    => $penjualanBulanIni,
            'pembelianBulanIni'    => $pembelianBulanIni,
            'obatHabis'            => $obatHabis,
            'obatStokKritis'       => $obatStokKritis,
            'obatStokRendah'       => $obatStokRendah,
            'trendLabels'          => $trendLabels,
            'trendPenjualan'       => $trendPenjualan,
            'trendTransaksi'       => $trendTransaksi,
            'topObats'             => $topObats,
            'recentPenjualans'     => $recentPenjualans,
            'metodeBreakdown'      => $metodeBreakdown,
            'penjualanPerKaryawan' => $penjualanPerKaryawan,
        ]);
    }

    /**
     * Query builder Penjualan yang di-scope ke user tertentu (untuk karyawan)
     * atau tidak di-scope (untuk admin/owner).
     */
    private function scopedPenjualan(?User $user): Builder
    {
        $query = Penjualan::query();
        if ($user !== null) {
            $query->where('user_id', $user->id);
        }
        return $query;
    }
}
