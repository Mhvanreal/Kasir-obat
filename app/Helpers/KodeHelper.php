<?php

namespace App\Helpers;

use App\Models\Pelanggan;
use App\Models\Supplier;

/**
 * Helper untuk membuat kode otomatis berurutan pada tabel master
 * yang menggunakan string primary key (kd_supplier, kd_pelanggan, ...).
 */
class KodeHelper
{
    public const SUPPLIER_PREFIX = 'SUP';

    public const PELANGGAN_PREFIX = 'PLG';

    /**
     * Membuat kode supplier baru, misal SUP004 (lanjutan SUP001-003).
     */
    public static function supplier(): string
    {
        $last = Supplier::query()
            ->orderByDesc('kd_supplier')
            ->value('kd_supplier');

        return self::nextCode(self::SUPPLIER_PREFIX, $last);
    }

    /**
     * Membuat kode pelanggan baru, misal PLG004 (lanjutan PLG001-003).
     */
    public static function pelanggan(): string
    {
        $last = Pelanggan::query()
            ->orderByDesc('kd_pelanggan')
            ->value('kd_pelanggan');

        return self::nextCode(self::PELANGGAN_PREFIX, $last);
    }

    /**
     * Menghitung kode berikutnya berdasarkan kode terakhir di database.
     */
    protected static function nextCode(string $prefix, ?string $last): string
    {
        $number = $last ? (int) substr($last, strlen($prefix)) : 0;

        return $prefix.str_pad((string) ($number + 1), 3, '0', STR_PAD_LEFT);
    }
}
