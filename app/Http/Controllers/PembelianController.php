<?php

namespace App\Http\Controllers;

use App\Http\Requests\PembelianRequest;
use App\Models\Obat;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class PembelianController extends Controller
{
    /**
     * Display the restock (pembelian stok) page.
     */
    public function index()
    {
        $suppliers = Supplier::all();
        $obats = Obat::with('supplier')->get();
        $pembelians = Pembelian::with('supplier', 'user')
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        return view('admin.pembelian', compact('suppliers', 'obats', 'pembelians'));
    }

    /**
     * Store a new pembelian transaction (restock + harga beli terbaru).
     */
    public function store(PembelianRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated) {
                $nota = 'PBL-'.date('ymdHis').'-'.strtoupper(substr(uniqid(), -3));

                $total = 0;
                foreach ($validated['items'] as $item) {
                    $obat = Obat::where('kd_obat', $item['kd_obat'])->lockForUpdate()->firstOrFail();
                    $hargaBeli = $item['harga_beli'] ?? $obat->harga_beli;
                    $total += $hargaBeli * $item['jumlah'];
                }

                $diskon = $validated['diskon'] ?? 0;
                $grandTotal = $total - (($total * $diskon) / 100);

                Pembelian::create([
                    'nota' => $nota,
                    'tgl_nota' => now(),
                    'kd_supplier' => $validated['kd_supplier'],
                    'diskon' => $diskon,
                    'total' => $total,
                    'grand_total' => $grandTotal,
                    'user_id' => auth()->id(),
                ]);

                foreach ($validated['items'] as $item) {
                    $obat = Obat::where('kd_obat', $item['kd_obat'])->lockForUpdate()->firstOrFail();
                    $hargaBeli = $item['harga_beli'] ?? $obat->harga_beli;

                    PembelianDetail::create([
                        'nota' => $nota,
                        'kd_obat' => $item['kd_obat'],
                        'jumlah' => $item['jumlah'],
                        'harga_beli' => $hargaBeli,
                        'subtotal' => $hargaBeli * $item['jumlah'],
                    ]);

                    $obat->increment('stok', $item['jumlah']);
                    $obat->update(['harga_beli' => $hargaBeli]);
                }
            });

            return redirect()->route('admin.pembelian')
                ->with('success', 'Pembelian stok berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Pembelian gagal: '.$e->getMessage())
                ->withInput();
        }
    }
}
