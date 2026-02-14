<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $primaryKey = 'kd_obat';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_obat',
        'nm_obat',
        'jenis',
        'satuan',
        'harga_beli',
        'harga_jual',
        'stok',
        'kd_supplier',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'stok' => 'integer',
    ];

    /**
     * Relasi ke Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'kd_supplier', 'kd_supplier');
    }

    /**
     * Relasi ke Penjualan Detail
     */
    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class, 'kd_obat', 'kd_obat');
    }

    /**
     * Relasi ke Pembelian Detail
     */
    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class, 'kd_obat', 'kd_obat');
    }
}
