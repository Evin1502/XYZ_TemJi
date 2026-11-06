<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class KategoriDaurUlang extends Model
{
    use HasFactory;

    protected $table = 'kategori_daur_ulang';

    protected $fillable = [
        'nama',
        'tipe',
        'satuan',
        'icon',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Seluruh riwayat tarif untuk kategori ini.
     */
    public function riwayatTarif(): HasMany
    {
        return $this->hasMany(RiwayatTarifPoin::class, 'kategori_id')->orderBy('berlaku_sejak', 'desc');
    }

    /**
     * Tarif poin yang sedang berlaku saat ini.
     */
    public function tarifAktif(): HasOne
    {
        return $this->hasOne(RiwayatTarifPoin::class, 'kategori_id')
            ->ofMany([
                'berlaku_sejak' => 'max',
                'id' => 'max',
            ], function ($query) {
                $query->where('berlaku_sejak', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('berlaku_hingga')
                          ->orWhere('berlaku_hingga', '>=', now());
                    });
            });
    }

    /**
     * Scope untuk kategori aktif (hanya kategori aktif yang muncul di form pengajuan daur ulang).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Poin per kg yang aktif saat ini.
     */
    public function getPoinSaatIniAttribute(): int
    {
        return $this->tarifAktif?->poin_per_kg ?? 0;
    }
}
