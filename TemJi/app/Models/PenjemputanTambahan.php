<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjemputanTambahan extends Model
{
    public const STATUS_MENUNGGU   = 'menunggu';
    public const STATUS_DISETUJUI  = 'disetujui';
    public const STATUS_DITOLAK    = 'ditolak';
    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUS = [
        self::STATUS_MENUNGGU   => 'Menunggu',
        self::STATUS_DISETUJUI  => 'Disetujui',
        self::STATUS_DITOLAK    => 'Ditolak',
        self::STATUS_DIBATALKAN => 'Dibatalkan',
    ];

    protected $table = 'penjemputan_tambahan';

    protected $fillable = [
        'kode',
        'user_id',
        'wilayah_id',
        'tanggal_diminta',
        'alasan',
        'status',
    ];

    protected $casts = [
        'tanggal_diminta' => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeMenunggu(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    public function isMenunggu(): bool
    {
        return $this->status === self::STATUS_MENUNGGU;
    }

    /**
     * Generator kode pengajuan unik per bulan, mis. PT-2611-0001.
     */
    public static function generateKode(?Carbon $waktu = null): string
    {
        $prefix = 'PT-' . ($waktu ?? Carbon::now())->format('ym') . '-';

        $terakhir = self::where('kode', 'like', $prefix . '%')->orderByDesc('kode')->value('kode');
        $urutan = $terakhir ? (int) substr($terakhir, strlen($prefix)) + 1 : 1;

        return $prefix . str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status] ?? ucfirst((string) $this->status);
    }

    /**
     * Kelas warna badge status untuk UI.
     */
    public function getBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DISETUJUI  => 'bg-emerald-50 border-emerald-200 text-emerald-700',
            self::STATUS_DITOLAK    => 'bg-rose-50 border-rose-200 text-rose-700',
            self::STATUS_DIBATALKAN => 'bg-slate-100 border-slate-200 text-slate-500',
            default                 => 'bg-amber-50 border-amber-200 text-amber-700',
        };
    }
}
