<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * Tabel Pembelian: Menyimpan data header/induk transaksi pembelian dari supplier
     */
    public function up(): void
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->string('nota', 20)->primary();
            $table->date('tgl_nota');
            $table->string('kd_supplier', 10)->nullable();
            $table->decimal('diskon', 5, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('kd_supplier')
                ->references('kd_supplier')
                ->on('suppliers')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Index untuk pencarian
            $table->index('tgl_nota');
            $table->index('kd_supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
