<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajuanDaurUlangItem extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_daur_ulang_items';

    protected $fillable = [
        'pengajuan_id',
        'kategori_id',
        'estimasi_berat_kg',
        'poin_per_kg_snapshot',
        'estimasi_poin',
    ];

    protected $casts = [
        'estimasi_berat_kg' => 'integer',
        'poin_per_kg_snapshot' => 'integer',
        'estimasi_poin' => 'integer',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(PengajuanDaurUlang::class, 'pengajuan_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriDaurUlang::class, 'kategori_id');
    }

    public function getLabelRingkasAttribute(): string
    {
        $nama = $this->kategori?->nama ?? 'Item';
        return "{$nama} ± {$this->estimasi_berat_kg} kg";
    }
}
