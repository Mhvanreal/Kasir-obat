<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengeluaranRequest;
use App\Models\Pengeluaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    /**
     * Halaman utama pengeluaran: KPI + filter + tabel CRUD (modal).
     */
    public function index(Request $request)
    {
        $request->validate([
            'jenis'          => 'nullable|in:operasional,gaji',
            'bulan'          => 'nullable|integer|min:1|max:12',
            'tahun'          => 'nullable|integer|min:2000|max:2100',
            'tanggal_dari'   => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
        ]);

        $today = Carbon::today();
        $bulan = (int) $request->input('bulan', $today->month);
        $tahun = (int) $request->input('tahun', $today->year);
        $jenis = $request->input('jenis');
        $dari  = $request->input('tanggal_dari');
        $sampai = $request->input('tanggal_sampai');

        $query = Pengeluaran::with('karyawan', 'pencatat')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        if ($dari && $sampai) {
            $query->whereBetween('tanggal', [$dari, $sampai]);
        } else {
            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        }

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        $pengeluarans = $query->paginate(20)->withQueryString();

        // KPI: hitung ulang tanpa filter jenis supaya rangkuman lengkap.
        $kpiQuery = Pengeluaran::query();
        if ($dari && $sampai) {
            $kpiQuery->whereBetween('tanggal', [$dari, $sampai]);
        } else {
            $kpiQuery->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        }
        $totalOperasional = (clone $kpiQuery)->where('jenis', Pengeluaran::JENIS_OPERASIONAL)->sum('jumlah');
        $totalGaji = (clone $kpiQuery)->where('jenis', Pengeluaran::JENIS_GAJI)->sum('jumlah');
        $totalPengeluaran = $totalOperasional + $totalGaji;

        // Karyawan aktif untuk dropdown penerima gaji.
        $karyawans = User::where('role', 'karyawan')->orderBy('name')->get(['id', 'name', 'email']);

        return view('owner.pengeluaran.index', [
            'pengeluarans'     => $pengeluarans,
            'karyawans'        => $karyawans,
            'jenis'            => $jenis,
            'bulan'            => $bulan,
            'tahun'            => $tahun,
            'dari'             => $dari,
            'sampai'           => $sampai,
            'totalOperasional' => $totalOperasional,
            'totalGaji'        => $totalGaji,
            'totalPengeluaran' => $totalPengeluaran,
        ]);
    }

    public function store(PengeluaranRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        Pengeluaran::create($data);

        return redirect()->route('owner.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dicatat.');
    }

    public function update(PengeluaranRequest $request, Pengeluaran $pengeluaran)
    {
        $pengeluaran->update($request->validated());

        return redirect()->route('owner.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        $pengeluaran->delete();

        return redirect()->route('owner.pengeluaran.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
