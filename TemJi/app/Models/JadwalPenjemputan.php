<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JadwalPenjemputan extends Model
{
    public const JENIS_SAMPAH_BIASA = 'sampah_biasa';
    public const JENIS_DAUR_ULANG = 'daur_ulang';

    public const JENIS = [
        self::JENIS_SAMPAH_BIASA => 'Sampah biasa',
        self::JENIS_DAUR_ULANG => 'Daur ulang',
    ];

    public const HARI = [
        1 => 'Sen',
        2 => 'Sel',
        3 => 'Rab',
        4 => 'Kam',
        5 => 'Jum',
        6 => 'Sab',
    ];

    protected $table = 'jadwal_penjemputan';

    protected $fillable = [
        'wilayah_id',
        'petugas_id',
        'jenis',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kuota',
    ];

    protected $casts = [
        'hari' => 'integer',
        'kuota' => 'integer',
    ];

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function scopeUntukWilayah(Builder $query, ?int $wilayahId): Builder
    {
        return $wilayahId ? $query->where('wilayah_id', $wilayahId) : $query;
    }

    public function isDaurUlang(): bool
    {
        return $this->jenis === self::JENIS_DAUR_ULANG;
    }

    /**
     * Jam disimpan seragam "H:i:s" agar perbandingan jam konsisten di MySQL maupun SQLite.
     */
    protected function jamMulai(): Attribute
    {
        return Attribute::set(fn (string $jam) => self::normalisasiJam($jam));
    }

    protected function jamSelesai(): Attribute
    {
        return Attribute::set(fn (string $jam) => self::normalisasiJam($jam));
    }

    /**
     * Rentang jam dengan format penulisan Indonesia, mis. "06.00–08.00".
     */
    protected function rentangWaktu(): Attribute
    {
        return Attribute::get(fn () => self::formatJam($this->jam_mulai) . '–' . self::formatJam($this->jam_selesai));
    }

    public static function normalisasiJam(string $jam): string
    {
        return Carbon::parse($jam)->format('H:i:s');
    }

    public static function formatJam(string $jam): string
    {
        return str_replace(':', '.', substr($jam, 0, 5));
    }

    /**
     * Jumlah rumah yang sudah memilih slot ini pada tanggal tertentu.
     * Akan dihitung dari pengajuan daur ulang warga setelah tabel pengajuan tersedia.
     */
    public function jumlahTerisi(Carbon $tanggal): int
    {
        return 0;
    }

    public function isPenuh(Carbon $tanggal): bool
    {
        return $this->isDaurUlang() && $this->jumlahTerisi($tanggal) >= $this->kuota;
    }

    /**
     * Cek apakah petugas sudah punya jadwal lain yang jamnya tumpang-tindih di hari yang sama.
     */
    public static function adaBentrok(int $petugasId, int $hari, string $jamMulai, string $jamSelesai, ?int $kecualiId = null): bool
    {
        return self::query()
            ->where('petugas_id', $petugasId)
            ->where('hari', $hari)
            ->where('jam_mulai', '<', self::normalisasiJam($jamSelesai))
            ->where('jam_selesai', '>', self::normalisasiJam($jamMulai))
            ->when($kecualiId, fn (Builder $q) => $q->whereKeyNot($kecualiId))
            ->exists();
    }
}
