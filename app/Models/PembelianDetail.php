<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'nota',
        'kd_obat',
        'jumlah',
        'harga_beli',
        'subtotal',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_beli' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relasi ke Pembelian
     */
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'nota', 'nota');
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
        $this->subtotal = $this->jumlah * $this->harga_beli;

        return $this->subtotal;
    }
}
