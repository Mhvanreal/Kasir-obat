<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $primaryKey = 'nota';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'nota',
        'tgl_nota',
        'kd_supplier',
        'diskon',
        'total',
        'grand_total',
        'user_id',
    ];

    protected $casts = [
        'tgl_nota' => 'date',
        'diskon' => 'decimal:2',
        'total' => 'decimal:2',
        'grand_total' => 'decimal:2',
    ];

    /**
     * Relasi ke Supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'kd_supplier', 'kd_supplier');
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Pembelian Detail
     */
    public function details()
    {
        return $this->hasMany(PembelianDetail::class, 'nota', 'nota');
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
