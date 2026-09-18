<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota',
        'kd_obat',
        'jumlah',
        'harga_jual',
        'subtotal',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_jual' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relasi ke Penjualan
     */
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'nota', 'nota');
    }

    /**
     * Relasi ke Obat
     */
    public function obat()
    {
        return $this->belongsTo(Obat::class, 'kd_obat', 'kd_obat');
    }

    /**
     * Calculate subtotal
     */
    public function calculateSubtotal()
    {
        $this->subtotal = $this->jumlah * $this->harga_jual;

        return $this->subtotal;
    }
}
