<?php

namespace App\Http\Controllers;

use App\Helpers\KodeHelper;
use App\Http\Requests\PelangganRequest;
use App\Models\Pelanggan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    protected const REDIRECT_ROUTE = 'admin.pelanggan.index';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggans = Pelanggan::withCount('penjualans')
            ->latest()
            ->get();

        $totalTransaksi = $pelanggans->sum('penjualans_count');

        return view('admin.pelanggan', compact('pelanggans', 'totalTransaksi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PelangganRequest $request)
    {
        $pelanggan = Pelanggan::create([
            'kd_pelanggan' => KodeHelper::pelanggan(),
            ...$request->validated(),
        ]);

        return redirect()->route(self::REDIRECT_ROUTE)
            ->with('success', 'Data pelanggan "'.$pelanggan->nm_pelanggan.'" berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PelangganRequest $request, string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($request->validated());

        return redirect()->route(self::REDIRECT_ROUTE)
            ->with('success', 'Data pelanggan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        if ($pelanggan->penjualans()->exists()) {
            return redirect()->route(self::REDIRECT_ROUTE)
                ->with('error', 'Pelanggan tidak dapat dihapus karena memiliki riwayat transaksi.');
        }

        $pelanggan->delete();

        return redirect()->route(self::REDIRECT_ROUTE)
            ->with('success', 'Data pelanggan berhasil dihapus!');
    }

    /**
     * Tambah pelanggan baru secara cepat dari halaman transaksi (AJAX).
     * Hanya membutuhkan nama; kode dibuat otomatis.
     */
    public function quickStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nm_pelanggan' => 'required|string|max:100',
            'telpon' => 'nullable|string|max:20',
            'kota' => 'nullable|string|max:50',
            'alamat' => 'nullable|string|max:500',
        ], [
            'nm_pelanggan.required' => 'Nama pelanggan wajib diisi',
            'nm_pelanggan.max' => 'Nama pelanggan maksimal 100 karakter',
        ]);

        $pelanggan = Pelanggan::create([
            'kd_pelanggan' => KodeHelper::pelanggan(),
            ...$validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pelanggan berhasil ditambahkan',
            'pelanggan' => $pelanggan,
        ]);
    }
}
