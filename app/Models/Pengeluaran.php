<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pengeluaran extends Model
{
    use HasFactory;

    public const JENIS_OPERASIONAL = 'operasional';
    public const JENIS_GAJI = 'gaji';

    protected $fillable = [
        'tanggal',
        'jenis',
        'karyawan_id',
        'deskripsi',
        'jumlah',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    /** Karyawan yang menerima gaji (untuk jenis=gaji). */
    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    /** User yang mencatat pengeluaran (biasanya owner). */
    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Alias supaya konsisten dengan model lain (Penjualan::user()). */
    public function user(): BelongsTo
    {
        return $this->pencatat();
    }
}
