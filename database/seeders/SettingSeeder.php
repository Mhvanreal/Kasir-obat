<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'key' => 'qris_image',
            'value' => null,
            'type' => 'image',
            'description' => 'Path gambar QR Code untuk pembayaran QRIS',
        ]);

        Setting::create([
            'key' => 'qris_enabled',
            'value' => '0',
            'type' => 'boolean',
            'description' => 'Status aktif/nonaktif pembayaran QRIS (0=nonaktif, 1=aktif)',
        ]);

        Setting::create([
            'key' => 'nama_toko',
            'value' => 'Apotek Citra',
            'type' => 'string',
            'description' => 'Nama toko/apotek',
        ]);
    }
}
