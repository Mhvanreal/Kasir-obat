<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique()->comment('Unique key untuk setting');
            $table->text('value')->nullable()->comment('Value dari setting (bisa path file, text, json, dll)');
            $table->string('type', 50)->default('string')->comment('Tipe data: string, text, image, json');
            $table->text('description')->nullable()->comment('Deskripsi setting');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
