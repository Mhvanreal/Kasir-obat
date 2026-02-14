<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabel Obat: Menyimpan data master obat/produk farmasi
     */
    public function up(): void
    {
        Schema::create('obats', function (Blueprint $table) {
            $table->string('kd_obat', 10)->primary();
            $table->string('nm_obat', 150);
            $table->string('jenis', 50)->nullable();
            $table->string('satuan', 20)->default('PCS');
            $table->decimal('harga_beli', 12, 2);
            $table->decimal('harga_jual', 12, 2);
            $table->integer('stok')->default(0);
            $table->string('kd_supplier', 10)->nullable();
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('kd_supplier')
                ->references('kd_supplier')
                ->on('suppliers')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Index untuk pencarian
            $table->index('nm_obat');
            $table->index('jenis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obats');
    }
};
