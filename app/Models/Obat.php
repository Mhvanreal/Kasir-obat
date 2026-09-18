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
        'gambar',
        'kd_supplier',
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'stok' => 'integer',
    ];

    // ==== Ambang batas stok (dipakai di UI + query) ====
    /** Stok < AMBANG_KRITIS → status "kritis" (merah, harus segera restok). */
    public const AMBANG_KRITIS = 10;

    /** Stok <= AMBANG_RENDAH → status "rendah" (kuning, warning). */
    public const AMBANG_RENDAH = 20;

    /**
     * Status stok terkategorisasi: habis | kritis | rendah | aman.
     * Dipakai di Blade & JSON payload untuk konsistensi warna/badge di seluruh app.
     */
    public function getStatusStokAttribute(): string
    {
        if ($this->stok <= 0) {
            return 'habis';
        }
        if ($this->stok < self::AMBANG_KRITIS) {
            return 'kritis';
        }
        if ($this->stok <= self::AMBANG_RENDAH) {
            return 'rendah';
        }
        return 'aman';
    }

    public function isStokKritis(): bool
    {
        return $this->stok > 0 && $this->stok < self::AMBANG_KRITIS;
    }

    public function isStokRendah(): bool
    {
        return $this->stok >= self::AMBANG_KRITIS && $this->stok <= self::AMBANG_RENDAH;
    }

    public function isStokHabis(): bool
    {
        return $this->stok <= 0;
    }

    /** Query scope: obat dengan stok kritis (1..9). */
    public function scopeStokKritis($query)
    {
        return $query->whereBetween('stok', [1, self::AMBANG_KRITIS - 1]);
    }

    /** Query scope: obat dengan stok rendah (10..20). */
    public function scopeStokRendah($query)
    {
        return $query->whereBetween('stok', [self::AMBANG_KRITIS, self::AMBANG_RENDAH]);
    }

    /** Query scope: obat habis (stok <= 0). */
    public function scopeStokHabis($query)
    {
        return $query->where('stok', '<=', 0);
    }

    /** Query scope: butuh perhatian (habis + kritis + rendah = stok <= AMBANG_RENDAH). */
    public function scopeButuhPerhatian($query)
    {
        return $query->where('stok', '<=', self::AMBANG_RENDAH);
    }

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
