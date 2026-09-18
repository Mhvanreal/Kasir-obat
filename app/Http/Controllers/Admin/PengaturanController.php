<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    /**
     * Display pengaturan page
     */
    public function index()
    {
        $qrisImage = Setting::get('qris_image');
        $qrisEnabled = Setting::get('qris_enabled', '0');
        $namaToko = Setting::get('nama_toko', 'Apotek Citra');

        return view('admin.pengaturan', compact('qrisImage', 'qrisEnabled', 'namaToko'));
    }

    /**
     * Update QRIS settings
     */
    public function updateQris(Request $request)
    {
        $currentQrisImage = Setting::get('qris_image');
        
        // Jika mengaktifkan QRIS dan belum ada QR Code, wajib upload
        $imageRequired = $request->qris_enabled == '1' && !$currentQrisImage;
        
        $request->validate([
            'qris_enabled' => 'required|in:0,1',
            'qris_image' => [
                $imageRequired ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg',
                'max:2048'
            ],
        ], [
            'qris_image.required' => 'QR Code wajib diupload untuk mengaktifkan QRIS',
            'qris_image.image' => 'File harus berupa gambar',
            'qris_image.mimes' => 'Gambar harus berformat jpeg, png, atau jpg',
            'qris_image.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        // Jika nonaktif dan tidak upload file baru, simpan status saja
        if ($request->qris_enabled == '0' && !$request->hasFile('qris_image')) {
            Setting::set('qris_enabled', '0', 'boolean', 'Status aktif/nonaktif pembayaran QRIS');
            return redirect()->route('admin.pengaturan.index')
                ->with('success', 'QRIS berhasil dinonaktifkan');
        }

        // Update qris_enabled
        Setting::set('qris_enabled', $request->qris_enabled, 'boolean', 'Status aktif/nonaktif pembayaran QRIS');

        // Handle file upload
        if ($request->hasFile('qris_image')) {
            // Delete old image if exists
            $oldImage = Setting::get('qris_image');
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            // Store new image
            $path = $request->file('qris_image')->store('qris', 'public');
            Setting::set('qris_image', $path, 'image', 'Path gambar QR Code untuk pembayaran QRIS');
        }

        $message = $request->qris_enabled == '1' 
            ? 'Pengaturan QRIS berhasil diperbarui dan diaktifkan' 
            : 'Pengaturan QRIS berhasil diperbarui';

        return redirect()->route('admin.pengaturan.index')->with('success', $message);
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
        ]);

        Setting::set('nama_toko', $request->nama_toko, 'string', 'Nama toko/apotek');

        return redirect()->route('admin.pengaturan.index')->with('success', 'Pengaturan umum berhasil diperbarui');
    }
}
