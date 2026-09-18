<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    protected $primaryKey = 'nota';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nota',
        'tgl_nota',
        'kd_pelanggan',
        'diskon',
        'total',
        'grand_total',
        'metode_pembayaran',
        'user_id',
    ];

    protected $casts = [
        'tgl_nota' => 'date',
        'diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    /**
     * Relasi ke Pelanggan
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'kd_pelanggan', 'kd_pelanggan');
    }

    /**
     * Relasi ke User (Kasir)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Penjualan Detail
     */
    public function details()
    {
        return $this->hasMany(PenjualanDetail::class, 'nota', 'nota');
    }

    /**
     * Calculate grand total after discount
     */
    public function calculateGrandTotal()
    {
        $diskonAmount = ($this->total * $this->diskon) / 100;
        $this->grand_total = $this->total - $diskonAmount;

        return $this->grand_total;
    }
}
