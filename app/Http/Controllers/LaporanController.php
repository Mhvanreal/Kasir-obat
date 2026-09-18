<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    private const PERIODE_OPTIONS = [
        'harian'   => 'Harian',
        'mingguan' => 'Mingguan',
        'bulanan'  => 'Bulanan',
    ];

    /**
     * Preset rentang cepat.
     */
    private const RANGE_PRESETS = [
        'today'   => 'Hari Ini',
        '7d'      => '7 Hari Terakhir',
        '30d'     => '30 Hari Terakhir',
        'month'   => 'Bulan Ini',
        'lastmonth' => 'Bulan Lalu',
        'year'    => 'Tahun Ini',
        'custom'  => 'Kustom',
    ];

    public function index(Request $request)
    {
        $this->validateFilter($request);

        [$dari, $sampai, $rangeKey] = $this->resolveRange($request);
        $periode = $request->input('periode', 'harian');
        $metode = $request->input('metode_pembayaran');

        $baseQuery = Penjualan::query()
            ->whereBetween('tgl_nota', [$dari, $sampai]);

        if ($metode) {
            $baseQuery->where('metode_pembayaran', $metode);
        }

        // ==== KPI ringkasan ====
        $totalPenjualan = (clone $baseQuery)->sum('grand_total');
        $totalTransaksi = (clone $baseQuery)->count();
        $totalDiskon    = (float) (clone $baseQuery)
            ->selectRaw('COALESCE(SUM(total - grand_total), 0) as total_diskon')
            ->value('total_diskon');
        $rataRata = $totalTransaksi > 0 ? $totalPenjualan / $totalTransaksi : 0;

        // Keuntungan estimasi (revenue - HPP saat ini). HPP dihitung dari obats.harga_beli terkini.
        $keuntunganEstimasi = (float) DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'pd.nota', '=', 'p.nota')
            ->join('obats as o', 'pd.kd_obat', '=', 'o.kd_obat')
            ->whereBetween('p.tgl_nota', [$dari, $sampai])
            ->when($metode, fn($q) => $q->where('p.metode_pembayaran', $metode))
            ->selectRaw('COALESCE(SUM(pd.subtotal - (pd.jumlah * o.harga_beli)), 0) as keuntungan')
            ->value('keuntungan');

        // ==== Chart penjualan per periode ====
        [$chartLabels, $chartPenjualan, $chartTransaksi] = $this->buildPeriodeChart($dari, $sampai, $periode, $metode);

        // ==== Distribusi metode pembayaran ====
        $metodeBreakdown = (clone $baseQuery)
            ->selectRaw('metode_pembayaran, COUNT(*) as jumlah, SUM(grand_total) as total')
            ->groupBy('metode_pembayaran')
            ->get();

        // ==== Top 10 obat terlaris ====
        $topObats = PenjualanDetail::query()
            ->whereHas('penjualan', function ($q) use ($dari, $sampai, $metode) {
                $q->whereBetween('tgl_nota', [$dari, $sampai]);
                if ($metode) {
                    $q->where('metode_pembayaran', $metode);
                }
            })
            ->selectRaw('kd_obat, SUM(jumlah) as total_terjual, SUM(subtotal) as total_pendapatan')
            ->groupBy('kd_obat')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->with('obat:kd_obat,nm_obat,satuan')
            ->get();

        // ==== Tabel transaksi ====
        $penjualans = (clone $baseQuery)
            ->with('pelanggan', 'user')
            ->orderBy('tgl_nota', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('laporan.index', [
            'dari'               => $dari,
            'sampai'             => $sampai,
            'periode'            => $periode,
            'metode'             => $metode,
            'rangeKey'           => $rangeKey,
            'rangePresets'       => self::RANGE_PRESETS,
            'periodeOptions'     => self::PERIODE_OPTIONS,
            'totalPenjualan'     => $totalPenjualan,
            'totalTransaksi'     => $totalTransaksi,
            'totalDiskon'        => $totalDiskon,
            'rataRata'           => $rataRata,
            'keuntunganEstimasi' => $keuntunganEstimasi,
            'chartLabels'        => $chartLabels,
            'chartPenjualan'     => $chartPenjualan,
            'chartTransaksi'     => $chartTransaksi,
            'metodeBreakdown'    => $metodeBreakdown,
            'topObats'           => $topObats,
            'penjualans'         => $penjualans,
        ]);
    }

    /**
     * Export daftar transaksi ke CSV. Filter yang berlaku persis sama dengan index().
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $this->validateFilter($request);
        [$dari, $sampai, ] = $this->resolveRange($request);
        $metode = $request->input('metode_pembayaran');

        $query = Penjualan::query()
            ->whereBetween('tgl_nota', [$dari, $sampai])
            ->with('pelanggan', 'user', 'details.obat');

        if ($metode) {
            $query->where('metode_pembayaran', $metode);
        }

        $query->orderBy('tgl_nota')->orderBy('created_at');

        $filename = 'laporan-penjualan-' . $dari . '-sd-' . $sampai . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            // BOM UTF-8 supaya Excel menampilkan karakter Indonesia dengan benar
            fwrite($out, "\xEF\xBB\xBF");

            // Header
            fputcsv($out, [
                'Nota', 'Tanggal', 'Kasir', 'Pelanggan', 'Metode Pembayaran',
                'Kode Obat', 'Nama Obat', 'Jumlah', 'Harga Jual', 'Subtotal',
                'Total', 'Diskon (%)', 'Grand Total',
            ]);

            $query->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $p) {
                    if ($p->details->isEmpty()) {
                        fputcsv($out, [
                            $p->nota,
                            $p->tgl_nota?->format('Y-m-d'),
                            $p->user->name ?? '-',
                            $p->pelanggan->nm_pelanggan ?? 'Umum',
                            $p->metode_pembayaran,
                            '', '', '', '', '',
                            $p->total,
                            $p->diskon,
                            $p->grand_total,
                        ]);
                        continue;
                    }
                    foreach ($p->details as $d) {
                        fputcsv($out, [
                            $p->nota,
                            $p->tgl_nota?->format('Y-m-d'),
                            $p->user->name ?? '-',
                            $p->pelanggan->nm_pelanggan ?? 'Umum',
                            $p->metode_pembayaran,
                            $d->kd_obat,
                            $d->obat->nm_obat ?? '-',
                            $d->jumlah,
                            $d->harga_jual,
                            $d->subtotal,
                            $p->total,
                            $p->diskon,
                            $p->grand_total,
                        ]);
                    }
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Halaman printable (HTML) yang bisa di-print jadi PDF via browser (Ctrl+P).
     * Layout portrait, tanpa sidebar/navbar aplikasi.
     */
    public function printReport(Request $request)
    {
        $this->validateFilter($request);
        [$dari, $sampai, $rangeKey] = $this->resolveRange($request);
        $periode = $request->input('periode', 'harian');
        $metode = $request->input('metode_pembayaran');

        $baseQuery = Penjualan::query()->whereBetween('tgl_nota', [$dari, $sampai]);
        if ($metode) {
            $baseQuery->where('metode_pembayaran', $metode);
        }

        $totalPenjualan = (clone $baseQuery)->sum('grand_total');
        $totalTransaksi = (clone $baseQuery)->count();
        $totalDiskon    = (float) (clone $baseQuery)
            ->selectRaw('COALESCE(SUM(total - grand_total), 0) as total_diskon')
            ->value('total_diskon');
        $rataRata = $totalTransaksi > 0 ? $totalPenjualan / $totalTransaksi : 0;

        $keuntunganEstimasi = (float) DB::table('penjualan_details as pd')
            ->join('penjualans as p', 'pd.nota', '=', 'p.nota')
            ->join('obats as o', 'pd.kd_obat', '=', 'o.kd_obat')
            ->whereBetween('p.tgl_nota', [$dari, $sampai])
            ->when($metode, fn($q) => $q->where('p.metode_pembayaran', $metode))
            ->selectRaw('COALESCE(SUM(pd.subtotal - (pd.jumlah * o.harga_beli)), 0) as keuntungan')
            ->value('keuntungan');

        $metodeBreakdown = (clone $baseQuery)
            ->selectRaw('metode_pembayaran, COUNT(*) as jumlah, SUM(grand_total) as total')
            ->groupBy('metode_pembayaran')
            ->get();

        $topObats = PenjualanDetail::query()
            ->whereHas('penjualan', function ($q) use ($dari, $sampai, $metode) {
                $q->whereBetween('tgl_nota', [$dari, $sampai]);
                if ($metode) {
                    $q->where('metode_pembayaran', $metode);
                }
            })
            ->selectRaw('kd_obat, SUM(jumlah) as total_terjual, SUM(subtotal) as total_pendapatan')
            ->groupBy('kd_obat')
            ->orderByDesc('total_terjual')
            ->limit(10)
            ->with('obat:kd_obat,nm_obat,satuan')
            ->get();

        $penjualans = (clone $baseQuery)
            ->with('pelanggan', 'user')
            ->orderBy('tgl_nota', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('laporan.print', compact(
            'dari', 'sampai', 'periode', 'metode',
            'totalPenjualan', 'totalTransaksi', 'totalDiskon', 'rataRata',
            'keuntunganEstimasi', 'metodeBreakdown', 'topObats', 'penjualans'
        ));
    }

    private function validateFilter(Request $request): void
    {
        $request->validate([
            'tanggal_dari'      => 'nullable|date',
            'tanggal_sampai'    => 'nullable|date|after_or_equal:tanggal_dari',
            'periode'           => 'nullable|in:harian,mingguan,bulanan',
            'metode_pembayaran' => 'nullable|in:cash,qris',
            'range'             => 'nullable|in:today,7d,30d,month,lastmonth,year,custom',
        ]);
    }

    /**
     * Tentukan rentang tanggal berdasarkan preset atau input manual.
     *
     * @return array{0: string, 1: string, 2: string} [tanggal_dari, tanggal_sampai, rangeKey]
     */
    private function resolveRange(Request $request): array
    {
        $today = Carbon::today();
        $rangeKey = $request->input('range');
        $dari = $request->input('tanggal_dari');
        $sampai = $request->input('tanggal_sampai');

        // Kalau user isi tanggal manual, prioritaskan itu (rangeKey = custom).
        if ($dari && $sampai) {
            return [$dari, $sampai, 'custom'];
        }

        switch ($rangeKey) {
            case 'today':
                return [$today->toDateString(), $today->toDateString(), 'today'];
            case '7d':
                return [$today->copy()->subDays(6)->toDateString(), $today->toDateString(), '7d'];
            case 'month':
                return [$today->copy()->startOfMonth()->toDateString(), $today->toDateString(), 'month'];
            case 'lastmonth':
                $start = $today->copy()->subMonthNoOverflow()->startOfMonth();
                $end = $today->copy()->subMonthNoOverflow()->endOfMonth();
                return [$start->toDateString(), $end->toDateString(), 'lastmonth'];
            case 'year':
                return [$today->copy()->startOfYear()->toDateString(), $today->toDateString(), 'year'];
            case '30d':
            default:
                return [$today->copy()->subDays(29)->toDateString(), $today->toDateString(), '30d'];
        }
    }

    /**
     * Bangun label + dataset chart penjualan per periode.
     * Selalu isi periode kosong (nilai 0) supaya grafik kontinu.
     *
     * @return array{0: array<string>, 1: array<float>, 2: array<int>}
     */
    private function buildPeriodeChart(string $dari, string $sampai, string $periode, ?string $metode): array
    {
        $dariC = Carbon::parse($dari);
        $sampaiC = Carbon::parse($sampai);

        // Ambil data agregat lalu isi periode kosong di PHP (portable, tidak tergantung DB).
        if ($periode === 'bulanan') {
            $format = 'Y-m';
            $displayFormat = 'M Y';
            $step = 'addMonthNoOverflow';
            $dariC = $dariC->startOfMonth();
            $sampaiC = $sampaiC->startOfMonth();
        } elseif ($periode === 'mingguan') {
            $format = 'o-W'; // ISO year + week number
            $displayFormat = '\M\g W - o';
            $step = 'addWeek';
            $dariC = $dariC->startOfWeek();
            $sampaiC = $sampaiC->startOfWeek();
        } else {
            $format = 'Y-m-d';
            $displayFormat = 'd M';
            $step = 'addDay';
        }

        $q = Penjualan::query()->whereBetween('tgl_nota', [$dari, $sampai]);
        if ($metode) {
            $q->where('metode_pembayaran', $metode);
        }

        $rows = $q->get(['tgl_nota', 'grand_total'])
            ->groupBy(fn($row) => Carbon::parse($row->tgl_nota)->format($format))
            ->map(fn($group) => [
                'total'  => (float) $group->sum('grand_total'),
                'jumlah' => $group->count(),
            ]);

        $labels = [];
        $penjualan = [];
        $transaksi = [];

        $cursor = $dariC->copy();
        $limit = 400; // safety
        while ($cursor->lte($sampaiC) && $limit-- > 0) {
            $key = $cursor->format($format);
            $labels[] = $cursor->format($displayFormat);
            $penjualan[] = (float) ($rows[$key]['total'] ?? 0);
            $transaksi[] = (int) ($rows[$key]['jumlah'] ?? 0);
            $cursor->{$step}();
        }

        return [$labels, $penjualan, $transaksi];
    }
}
