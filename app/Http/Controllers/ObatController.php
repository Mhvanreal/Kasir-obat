<?php

namespace App\Http\Controllers;

use App\Http\Requests\ObatRequest;
use App\Models\Obat;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;

class ObatController extends Controller
{
    protected const REDIRECT_ROUTE = 'karyawan.obat.index';

    protected const GAMBAR_DIR = 'gambar-obat';

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
    public function store(ObatRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('gambar')) {
            $validated['gambar'] = $request->file('gambar')->store(self::GAMBAR_DIR, 'public');
        }

        Obat::create($validated);

        return redirect()->route(self::REDIRECT_ROUTE)
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
    public function update(ObatRequest $request, string $id)
    {
        $obat = Obat::findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('gambar')) {
            if ($obat->gambar) {
                Storage::disk('public')->delete($obat->gambar);
            }

            $validated['gambar'] = $request->file('gambar')->store(self::GAMBAR_DIR, 'public');
        }

        $obat->update($validated);

        return redirect()->route(self::REDIRECT_ROUTE)
            ->with('success', 'Data obat berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $obat = Obat::findOrFail($id);

        if ($obat->gambar) {
            Storage::disk('public')->delete($obat->gambar);
        }

        $obat->delete();

        return redirect()->route(self::REDIRECT_ROUTE)
            ->with('success', 'Data obat berhasil dihapus!');
    }
}
