<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Change roles from 4 (admin, kasir, apoteker, owner) to 3 (admin, owner, karyawan).
     */
    public function up(): void
    {
        // 1. Temporarily widen the ENUM to accept the new value alongside old ones
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'kasir', 'apoteker', 'owner', 'karyawan'])->default('karyawan')->change();
        });

        // 2. Merge kasir + apoteker into karyawan
        DB::table('users')
            ->whereIn('role', ['kasir', 'apoteker'])
            ->update(['role' => 'karyawan']);

        // 3. Narrow the ENUM to the final 3 roles
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'owner', 'karyawan'])->default('karyawan')->change();
        });
    }

    /**
     * Reverse: restore old 4-role enum. Karyawan rows map back to kasir (default).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'kasir', 'apoteker', 'owner', 'karyawan'])->default('kasir')->change();
        });

        DB::table('users')
            ->where('role', 'karyawan')
            ->update(['role' => 'kasir']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'kasir', 'apoteker', 'owner'])->default('kasir')->change();
        });
    }
};
