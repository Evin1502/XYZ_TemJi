<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatTarifPoin extends Model
{
    use HasFactory;

    protected $table = 'riwayat_tarif_poin';

    protected $fillable = [
        'kategori_id',
        'poin_per_kg',
        'berlaku_sejak',
        'berlaku_hingga',
        'keterangan',
    ];

    protected $casts = [
        'berlaku_sejak' => 'datetime',
        'berlaku_hingga' => 'datetime',
        'poin_per_kg' => 'integer',
    ];

    /**
     * Kategori pemilik riwayat tarif ini.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriDaurUlang::class, 'kategori_id');
    }

    /**
     * Status tarif: Terjadwal, Berlaku, atau Selesai.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->berlaku_sejak && $this->berlaku_sejak->isFuture()) {
            return 'Terjadwal';
        }

        if ($this->berlaku_hingga && $this->berlaku_hingga->isPast()) {
            return 'Selesai';
        }

        return 'Berlaku';
    }

    /**
     * Format tampilan periode tanggal berlaku.
     */
    public function getPeriodeLabelAttribute(): string
    {
        $sejak = $this->berlaku_sejak ? Carbon::parse($this->berlaku_sejak)->translatedFormat('j M Y') : '-';

        if (!$this->berlaku_hingga) {
            return 'Mulai ' . $sejak;
        }

        $hingga = Carbon::parse($this->berlaku_hingga)->translatedFormat('j M Y');
        return $sejak . ' – ' . $hingga;
    }
}
