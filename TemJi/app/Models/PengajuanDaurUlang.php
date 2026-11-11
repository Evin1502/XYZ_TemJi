<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PengajuanDaurUlang extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU_JADWAL = 'menunggu_jadwal';
    public const STATUS_MENUNGGU        = 'menunggu';
    public const STATUS_DISETUJUI       = 'disetujui';
    public const STATUS_SELESAI         = 'selesai';
    public const STATUS_DIBATALKAN      = 'dibatalkan';

    protected $table = 'pengajuan_daur_ulang';

    protected $fillable = [
        'kode_pengajuan',
        'user_id',
        'wilayah_id',
        'jadwal_id',           // FK ke jadwal_penjemputan (nullable – antar mandiri tidak perlu jadwal)
        'total_estimasi_kg',
        'total_estimasi_poin',
        'status',
        'catatan',
    ];

    protected $casts = [
        'total_estimasi_kg'   => 'integer',
        'total_estimasi_poin' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relasi
    // -------------------------------------------------------------------------

    public function items(): HasMany
    {
        return $this->hasMany(PengajuanDaurUlangItem::class, 'pengajuan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    /**
     * Jadwal penjemputan yang dipilih warga (null untuk pengajuan antar mandiri).
     */
    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(JadwalPenjemputan::class, 'jadwal_id');
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    /**
     * Generator kode pengajuan deterministik unik, mis. DU-2610-0214 atau DU-2611-0001.
     */
    public static function generateKodePengajuan(?Carbon $waktu = null): string
    {
        $waktu  = $waktu ?? Carbon::now();
        $prefix = 'DU-' . $waktu->format('ym') . '-';

        $lastPengajuan = self::where('kode_pengajuan', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $urutan = 1;
        if ($lastPengajuan && preg_match('/' . preg_quote($prefix, '/') . '(\d+)/', $lastPengajuan->kode_pengajuan, $matches)) {
            $urutan = (int) $matches[1] + 1;
        }

        do {
            $kode = $prefix . str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
            $urutan++;
        } while (self::where('kode_pengajuan', $kode)->exists());

        return $kode;
    }

    /**
     * Ringkasan item daur ulang dengan format UI, mis:
     * "Minyak jelantah ± 2 kg · Botol PET bening ± 3 kg"
     */
    public function getRingkasanItemAttribute(): string
    {
        return $this->items
            ->map(function ($item) {
                $nama = $item->kategori?->nama ?? 'Sampah daur ulang';
                return "{$nama} ± {$item->estimasi_berat_kg} kg";
            })
            ->implode(' · ');
    }
}
