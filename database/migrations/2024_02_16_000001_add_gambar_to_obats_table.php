<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom gambar untuk foto/produk obat (nullable).
     */
    public function up(): void
    {
        Schema::table('obats', function (Blueprint $table) {
            $table->string('gambar', 255)->nullable()->after('stok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obats', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};
