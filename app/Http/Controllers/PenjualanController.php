<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    /**
     * Display the transaction page (list + form).
     */
    public function index()
    {
        $obats = Obat::with('supplier')->get();
        $pelanggans = Pelanggan::all();
        $penjualans = Penjualan::with('pelanggan', 'user')->orderBy('created_at', 'desc')->take(50)->get();
        
        // Get QRIS settings
        $qrisEnabled = Setting::get('qris_enabled', '0') == '1';
        $qrisImage = Setting::get('qris_image');

        return view('karyawan.transaksi', compact('obats', 'pelanggans', 'penjualans', 'qrisEnabled', 'qrisImage'));
    }

    /**
     * Store a new penjualan transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kd_pelanggan' => 'nullable|exists:pelanggans,kd_pelanggan',
            'diskon' => 'nullable|numeric|min:0|max:100',
            'metode_pembayaran' => 'required|in:cash,qris',
            'items' => 'required|array|min:1',
            'items.*.kd_obat' => 'required|exists:obats,kd_obat',
            'items.*.qty' => 'required|integer|min:1',
        ], [
            'items.required' => 'Minimal satu item harus ditambahkan',
            'items.min' => 'Minimal satu item harus ditambahkan',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih',
            'items.*.kd_obat.required' => 'Obat wajib dipilih',
            'items.*.qty.required' => 'Jumlah wajib diisi',
            'items.*.qty.min' => 'Jumlah minimal 1',
        ]);

        try {
            $nota = DB::transaction(function () use ($validated) {
                $nota = 'TRX-'.date('ymdHis').'-'.strtoupper(substr(uniqid(), -3));

                $total = 0;
                foreach ($validated['items'] as $item) {
                    $obat = Obat::where('kd_obat', $item['kd_obat'])->lockForUpdate()->first();
                    $subtotal = $obat->harga_jual * $item['qty'];
                    $total += $subtotal;
                }

                $diskon = $validated['diskon'] ?? 0;
                $grandTotal = $total - (($total * $diskon) / 100);

                Penjualan::create([
                    'nota' => $nota,
                    'tgl_nota' => now(),
                    'kd_pelanggan' => $validated['kd_pelanggan'] ?? null,
                    'diskon' => $diskon,
                    'total' => $total,
                    'grand_total' => $grandTotal,
                    'metode_pembayaran' => $validated['metode_pembayaran'],
                    'user_id' => auth()->id(),
                ]);

                foreach ($validated['items'] as $item) {
                    $obat = Obat::where('kd_obat', $item['kd_obat'])->lockForUpdate()->first();

                    if ($obat->stok < $item['qty']) {
                        throw new \Exception('Stok "'.$obat->nm_obat.'" tidak mencukupi.');
                    }

                    PenjualanDetail::create([
                        'nota' => $nota,
                        'kd_obat' => $item['kd_obat'],
                        'jumlah' => $item['qty'],
                        'harga_jual' => $obat->harga_jual,
                        'subtotal' => $obat->harga_jual * $item['qty'],
                    ]);

                    $obat->decrement('stok', $item['qty']);
                }

                return $nota;
            });

            return redirect()->route('karyawan.transaksi')
                ->with('success', 'Transaksi berhasil disimpan!')
                ->with('last_nota', $nota);
        } catch (\Exception $e) {
            return back()->with('error', 'Transaksi gagal: '.$e->getMessage())
                ->withInput();
        }
    }
}
