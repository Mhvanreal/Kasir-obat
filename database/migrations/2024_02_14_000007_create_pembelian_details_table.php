<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabel Pembelian Detail: Menyimpan data detail item yang dibeli per transaksi
     */
    public function up(): void
    {
        Schema::create('pembelian_details', function (Blueprint $table) {
            $table->id();
            $table->string('nota', 20);
            $table->string('kd_obat', 10);
            $table->integer('jumlah');
            $table->decimal('harga_beli', 12, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('nota')
                ->references('nota')
                ->on('pembelians')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('kd_obat')
                ->references('kd_obat')
                ->on('obats')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Composite Index
            $table->index(['nota', 'kd_obat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_details');
    }
};
