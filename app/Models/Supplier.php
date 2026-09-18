<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'kd_supplier';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kd_supplier',
        'nm_supplier',
        'alamat',
        'kota',
        'telpon',
    ];

    /**
     * Relasi ke Obat
     */
    public function obats()
    {
        return $this->hasMany(Obat::class, 'kd_supplier', 'kd_supplier');
    }

    /**
     * Relasi ke Pembelian
     */
    public function pembelians()
    {
        return $this->hasMany(Pembelian::class, 'kd_supplier', 'kd_supplier');
    }
}
