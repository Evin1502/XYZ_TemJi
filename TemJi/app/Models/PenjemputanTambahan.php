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
        'petugas_id',
        'tanggal_jemput',
        'jam_mulai',
        'jam_selesai',
        'alasan_penolakan',
        'diproses_oleh',
        'diproses_pada',
    ];

    protected $casts = [
        'tanggal_diminta' => 'date',
        'tanggal_jemput'  => 'date',
        'diproses_pada'   => 'datetime',
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

    /** Petugas yang ditugaskan saat pengajuan disetujui. */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /** Admin yang menyetujui atau menolak pengajuan. */
    public function pemroses(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeMenunggu(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_MENUNGGU);
    }

    /**
     * Penjemputan tambahan yang sudah disetujui dan dijadwalkan pada rentang tanggal tertentu.
     */
    public function scopeTerjadwalAntara(Builder $query, Carbon $awal, Carbon $akhir): Builder
    {
        return $query->where('status', self::STATUS_DISETUJUI)
            ->whereDate('tanggal_jemput', '>=', $awal->toDateString())
            ->whereDate('tanggal_jemput', '<=', $akhir->toDateString());
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    public function isMenunggu(): bool
    {
        return $this->status === self::STATUS_MENUNGGU;
    }

    public function isDisetujui(): bool
    {
        return $this->status === self::STATUS_DISETUJUI;
    }

    /**
     * Rentang jam jemput dengan format penulisan Indonesia, mis. "13.00–14.00".
     */
    public function getRentangWaktuAttribute(): ?string
    {
        if (! $this->jam_mulai || ! $this->jam_selesai) {
            return null;
        }

        return JadwalPenjemputan::formatJam($this->jam_mulai) . '–' . JadwalPenjemputan::formatJam($this->jam_selesai);
    }

    /**
     * Cek apakah petugas sudah bertugas pada tanggal dan jam tersebut, baik di jadwal rutin
     * mingguan maupun di penjemputan tambahan lain yang sudah disetujui.
     */
    public static function petugasBentrok(
        int $petugasId,
        Carbon $tanggal,
        string $jamMulai,
        string $jamSelesai,
        ?int $kecualiId = null,
    ): bool {
        if (JadwalPenjemputan::adaBentrok($petugasId, $tanggal->dayOfWeekIso, $jamMulai, $jamSelesai)) {
            return true;
        }

        return self::query()
            ->where('status', self::STATUS_DISETUJUI)
            ->where('petugas_id', $petugasId)
            ->whereDate('tanggal_jemput', $tanggal->toDateString())
            ->where('jam_mulai', '<', JadwalPenjemputan::normalisasiJam($jamSelesai))
            ->where('jam_selesai', '>', JadwalPenjemputan::normalisasiJam($jamMulai))
            ->when($kecualiId, fn (Builder $q) => $q->whereKeyNot($kecualiId))
            ->exists();
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
