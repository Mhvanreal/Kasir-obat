<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Pelanggan;
use App\Models\Obat;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Suppliers
        $suppliers = [
            [
                'kd_supplier' => 'SUP001',
                'nm_supplier' => 'PT Kimia Farma',
                'alamat' => 'Jl. Veteran No. 9',
                'kota' => 'Jakarta',
                'telpon' => '021-3841031',
            ],
            [
                'kd_supplier' => 'SUP002',
                'nm_supplier' => 'PT Kalbe Farma',
                'alamat' => 'Jl. Let. Jend. Suprapto',
                'kota' => 'Jakarta',
                'telpon' => '021-4212808',
            ],
            [
                'kd_supplier' => 'SUP003',
                'nm_supplier' => 'PT Sanbe Farma',
                'alamat' => 'Jl. Tambak Oso',
                'kota' => 'Bandung',
                'telpon' => '022-5201234',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        // Seed Pelanggans
        $pelanggans = [
            [
                'kd_pelanggan' => 'PLG001',
                'nm_pelanggan' => 'Umum',
                'alamat' => '-',
                'kota' => '-',
                'telpon' => '-',
            ],
            [
                'kd_pelanggan' => 'PLG002',
                'nm_pelanggan' => 'Budi Santoso',
                'alamat' => 'Jl. Merdeka No. 123',
                'kota' => 'Jakarta',
                'telpon' => '081234567890',
            ],
            [
                'kd_pelanggan' => 'PLG003',
                'nm_pelanggan' => 'Siti Nurhaliza',
                'alamat' => 'Jl. Sudirman No. 45',
                'kota' => 'Bandung',
                'telpon' => '081298765432',
            ],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::create($pelanggan);
        }

        // Seed Obats
        $obats = [
            [
                'kd_obat' => 'OBT001',
                'nm_obat' => 'Paracetamol 500mg',
                'jenis' => 'Tablet',
                'satuan' => 'STRIP',
                'harga_beli' => 2500,
                'harga_jual' => 3500,
                'stok' => 100,
                'kd_supplier' => 'SUP001',
            ],
            [
                'kd_obat' => 'OBT002',
                'nm_obat' => 'Amoxicillin 500mg',
                'jenis' => 'Kapsul',
                'satuan' => 'STRIP',
                'harga_beli' => 5000,
                'harga_jual' => 7000,
                'stok' => 80,
                'kd_supplier' => 'SUP002',
            ],
            [
                'kd_obat' => 'OBT003',
                'nm_obat' => 'OBH Combi Batuk',
                'jenis' => 'Sirup',
                'satuan' => 'BOTOL',
                'harga_beli' => 12000,
                'harga_jual' => 16000,
                'stok' => 50,
                'kd_supplier' => 'SUP001',
            ],
            [
                'kd_obat' => 'OBT004',
                'nm_obat' => 'Vitamin C 1000mg',
                'jenis' => 'Tablet',
                'satuan' => 'STRIP',
                'harga_beli' => 8000,
                'harga_jual' => 11000,
                'stok' => 120,
                'kd_supplier' => 'SUP002',
            ],
            [
                'kd_obat' => 'OBT005',
                'nm_obat' => 'Antangin JRG',
                'jenis' => 'Sachet',
                'satuan' => 'BOX',
                'harga_beli' => 15000,
                'harga_jual' => 20000,
                'stok' => 75,
                'kd_supplier' => 'SUP003',
            ],
            [
                'kd_obat' => 'OBT006',
                'nm_obat' => 'Bodrex Flu & Batuk',
                'jenis' => 'Tablet',
                'satuan' => 'BOX',
                'harga_beli' => 6000,
                'harga_jual' => 8500,
                'stok' => 90,
                'kd_supplier' => 'SUP001',
            ],
            [
                'kd_obat' => 'OBT007',
                'nm_obat' => 'Promag Tablet',
                'jenis' => 'Tablet',
                'satuan' => 'STRIP',
                'harga_beli' => 3500,
                'harga_jual' => 5000,
                'stok' => 110,
                'kd_supplier' => 'SUP002',
            ],
            [
                'kd_obat' => 'OBT008',
                'nm_obat' => 'Salep 88',
                'jenis' => 'Salep',
                'satuan' => 'TUBE',
                'harga_beli' => 18000,
                'harga_jual' => 24000,
                'stok' => 60,
                'kd_supplier' => 'SUP003',
            ],
        ];

        foreach ($obats as $obat) {
            Obat::create($obat);
        }

        $this->command->info('Master data seeded successfully!');
    }
}
