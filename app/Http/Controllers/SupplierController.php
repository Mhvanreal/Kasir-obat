<?php

namespace App\Http\Controllers;

use App\Helpers\KodeHelper;
use App\Http\Requests\SupplierRequest;
use App\Models\Supplier;

class SupplierController extends Controller
{
    protected const REDIRECT_ROUTE = 'admin.supplier.index';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $suppliers = Supplier::withCount('obats')
            ->withCount('pembelians')
            ->latest()
            ->get();

        $totalObat = $suppliers->sum('obats_count');

        return view('admin.supplier', compact('suppliers', 'totalObat'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupplierRequest $request)
    {
        $supplier = Supplier::create([
            'kd_supplier' => KodeHelper::supplier(),
            ...$request->validated(),
        ]);

        return redirect()->route(self::REDIRECT_ROUTE)
            ->with('success', 'Data supplier "'.$supplier->nm_supplier.'" berhasil ditambahkan!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SupplierRequest $request, string $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->update($request->validated());

        return redirect()->route(self::REDIRECT_ROUTE)
            ->with('success', 'Data supplier berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supplier = Supplier::findOrFail($id);

        if ($supplier->obats()->exists()) {
            return redirect()->route(self::REDIRECT_ROUTE)
                ->with('error', 'Supplier tidak dapat dihapus karena masih memiliki data obat.');
        }

        $supplier->delete();

        return redirect()->route(self::REDIRECT_ROUTE)
            ->with('success', 'Data supplier berhasil dihapus!');
    }
}
