<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $obats = Obat::with('supplier')->latest()->get();
        $suppliers = Supplier::all();
        return view('obat.index', compact('obats', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        return view('obat.create', compact('suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kd_obat' => 'required|string|max:20|unique:obats,kd_obat',
            'nm_obat' => 'required|string|max:255',
            'jenis' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kd_supplier' => 'required|exists:suppliers,kd_supplier',
        ], [
            'kd_obat.required' => 'Kode obat wajib diisi',
            'kd_obat.unique' => 'Kode obat sudah digunakan',
            'nm_obat.required' => 'Nama obat wajib diisi',
            'jenis.required' => 'Jenis obat wajib diisi',
            'satuan.required' => 'Satuan wajib diisi',
            'harga_beli.required' => 'Harga beli wajib diisi',
            'harga_jual.required' => 'Harga jual wajib diisi',
            'stok.required' => 'Stok wajib diisi',
            'kd_supplier.required' => 'Supplier wajib dipilih',
        ]);

        Obat::create($validated);

        return redirect()->route('apoteker.obat.index')
            ->with('success', 'Data obat berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $obat = Obat::with('supplier')->findOrFail($id);
        return view('obat.show', compact('obat'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $obat = Obat::findOrFail($id);
        $suppliers = Supplier::all();
        return view('obat.edit', compact('obat', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $obat = Obat::findOrFail($id);

        $validated = $request->validate([
            'nm_obat' => 'required|string|max:255',
            'jenis' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'kd_supplier' => 'required|exists:suppliers,kd_supplier',
        ], [
            'nm_obat.required' => 'Nama obat wajib diisi',
            'jenis.required' => 'Jenis obat wajib diisi',
            'satuan.required' => 'Satuan wajib diisi',
            'harga_beli.required' => 'Harga beli wajib diisi',
            'harga_jual.required' => 'Harga jual wajib diisi',
            'stok.required' => 'Stok wajib diisi',
            'kd_supplier.required' => 'Supplier wajib dipilih',
        ]);

        $obat->update($validated);

        return redirect()->route('apoteker.obat.index')
            ->with('success', 'Data obat berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return redirect()->route('apoteker.obat.index')
            ->with('success', 'Data obat berhasil dihapus!');
    }
}
