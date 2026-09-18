<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use Illuminate\Http\Request;

class RiwayatTransaksiController extends Controller
{
    /**
     * Display a listing of transactions with filters.
     */
    public function index(Request $request)
    {
        $query = Penjualan::with(['pelanggan', 'user', 'details.obat']);

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tgl_nota', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tgl_nota', '<=', $request->tanggal_sampai);
        }

        // Filter by payment method
        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->metode_pembayaran);
        }

        // Filter by customer
        if ($request->filled('kd_pelanggan')) {
            $query->where('kd_pelanggan', $request->kd_pelanggan);
        }

        // Search by nota
        if ($request->filled('search')) {
            $query->where('nota', 'like', '%' . $request->search . '%');
        }

        // Sort by latest first
        $penjualans = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get pelanggans for filter dropdown
        $pelanggans = \App\Models\Pelanggan::orderBy('nm_pelanggan')->get();

        return view('riwayat-transaksi.index', compact('penjualans', 'pelanggans'));
    }

    /**
     * Display the specified transaction detail.
     */
    public function show($nota)
    {
        $penjualan = Penjualan::with(['pelanggan', 'user', 'details.obat.supplier'])
            ->where('nota', $nota)
            ->firstOrFail();

        return view('riwayat-transaksi.show', compact('penjualan'));
    }

    /**
     * Generate PDF receipt for the transaction.
     * Returns a printable HTML view optimized for PDF printing.
     */
    public function cetakPdf($nota)
    {
        $penjualan = Penjualan::with(['pelanggan', 'user', 'details.obat'])
            ->where('nota', $nota)
            ->firstOrFail();

        return view('struk.pdf', compact('penjualan'));
    }

    /**
     * Generate thermal printer receipt for the transaction.
     * Returns a printable HTML view optimized for 58mm/80mm thermal printer.
     */
    public function cetakThermal($nota)
    {
        $penjualan = Penjualan::with(['pelanggan', 'user', 'details.obat'])
            ->where('nota', $nota)
            ->firstOrFail();

        return view('struk.thermal', compact('penjualan'));
    }
}
