<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        \App\Models\User::create([
            'name' => 'Administrator',
            'email' => 'admin@apotek.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // Create Kasir User
        \App\Models\User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@apotek.com',
            'password' => bcrypt('kasir123'),
            'role' => 'kasir',
        ]);

        // Create Apoteker User
        \App\Models\User::create([
            'name' => 'Apoteker',
            'email' => 'apoteker@gmail.com',
            'password' => bcrypt('apoteker123'),
            'role' => 'apoteker',
        ]);

        // Create Owner User
        \App\Models\User::create([
            'name' => 'Owner Apotek',
            'email' => 'owner@apotek.com',
            'password' => bcrypt('owner123'),
            'role' => 'owner',
        ]);

        // Seed Master Data (Supplier, Pelanggan, Obat)
        $this->call(MasterDataSeeder::class);
    }
}
