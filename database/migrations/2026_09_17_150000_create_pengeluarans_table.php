<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengeluaran apotek: operasional (listrik, sewa, dll) dan gaji karyawan.
 * Hanya bisa dikelola oleh owner.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->enum('jenis', ['operasional', 'gaji'])->index();
            // Referensi ke user (karyawan) yang menerima gaji. Nullable karena
            // pengeluaran operasional tidak terkait ke user manapun.
            $table->foreignId('karyawan_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('deskripsi', 255);
            $table->decimal('jumlah', 15, 2);
            $table->text('catatan')->nullable();
            // User yang mencatat pengeluaran (biasanya owner).
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengeluarans');
    }
};
