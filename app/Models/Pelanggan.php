<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $primaryKey = 'kd_pelanggan';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kd_pelanggan',
        'nm_pelanggan',
        'alamat',
        'kota',
        'telpon',
    ];

    /**
     * Relasi ke Penjualan
     */
    public function penjualans()
    {
        return $this->hasMany(Penjualan::class, 'kd_pelanggan', 'kd_pelanggan');
    }
}
