<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Warga extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_TERVERIFIKASI = 'terverifikasi';
    public const STATUS_DITOLAK = 'ditolak';

    protected $table = 'warga';

    protected $fillable = [
        'user_id',
        'nik',
        'no_kk',
        'nama_lengkap',
        'no_hp',
        'alamat',
        'wilayah_id',
        'foto_identitas',
        'status_verifikasi',
        'saldo_poin',
        'catatan_verifikasi',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    protected $casts = [
        'saldo_poin' => 'integer',
        'diverifikasi_pada' => 'datetime',
    ];

    /**
     * Akun kredensial login milik warga ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Wilayah domisili warga (Sukapura / Sukabirus).
     */
    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class, 'wilayah_id');
    }

    /**
     * Admin yang melakukan verifikasi identitas warga ini.
     */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /**
     * Rekam jejak seluruh transaksi mutasi poin milik warga ini.
     */
    public function riwayatPoin(): HasMany
    {
        return $this->hasMany(RiwayatPoin::class, 'warga_id')->orderByDesc('created_at')->orderByDesc('id');
    }

    /**
     * Tambah saldo poin warga dan catat transaksi masuk pada ledger riwayat_poin.
     */
    public function tambahPoin(
        int $poin,
        string $judul,
        ?string $keterangan = null,
        ?string $kodePengajuan = null,
        ?int $kategoriId = null,
        ?float $beratKg = null,
        ?Carbon $tanggal = null
    ): RiwayatPoin {
        return DB::transaction(function () use ($poin, $judul, $keterangan, $kodePengajuan, $kategoriId, $beratKg, $tanggal) {
            $this->increment('saldo_poin', $poin);
            $this->refresh();

            return $this->riwayatPoin()->create([
                'jenis' => 'masuk',
                'jumlah_poin' => $poin,
                'saldo_akhir' => $this->saldo_poin,
                'judul' => $judul,
                'keterangan' => $keterangan,
                'kode_pengajuan' => $kodePengajuan,
                'kategori_id' => $kategoriId,
                'berat_kg' => $beratKg,
                'created_at' => $tanggal ?? Carbon::now(),
                'updated_at' => $tanggal ?? Carbon::now(),
            ]);
        });
    }

    /**
     * Kurangi saldo poin warga dan catat transaksi keluar (potongan iuran) pada ledger riwayat_poin.
     */
    public function kurangiPoin(
        int $poin,
        string $judul,
        ?string $keterangan = null,
        ?Carbon $tanggal = null
    ): RiwayatPoin {
        return DB::transaction(function () use ($poin, $judul, $keterangan, $tanggal) {
            $potonganSebenarnya = min($this->saldo_poin, $poin);
            $this->decrement('saldo_poin', $potonganSebenarnya);
            $this->refresh();

            return $this->riwayatPoin()->create([
                'jenis' => 'keluar',
                'jumlah_poin' => $poin,
                'saldo_akhir' => $this->saldo_poin,
                'judul' => $judul,
                'keterangan' => $keterangan,
                'created_at' => $tanggal ?? Carbon::now(),
                'updated_at' => $tanggal ?? Carbon::now(),
            ]);
        });
    }

    /**
     * Scope untuk warga yang menunggu verifikasi identitas.
     */
    public function scopeMenunggu($query)
    {
        return $query->where('status_verifikasi', self::STATUS_MENUNGGU);
    }

    /**
     * Scope untuk warga yang identitasnya sudah terverifikasi.
     */
    public function scopeTerverifikasi($query)
    {
        return $query->where('status_verifikasi', self::STATUS_TERVERIFIKASI);
    }

    /**
     * Scope untuk warga yang pengajuan verifikasinya ditolak.
     */
    public function scopeDitolak($query)
    {
        return $query->where('status_verifikasi', self::STATUS_DITOLAK);
    }

    /**
     * Scope kompatibilitas untuk warga yang belum memiliki wilayah.
     */
    public function scopeBelumBerwilayah($query)
    {
        return $query->whereNull('wilayah_id');
    }

    /**
     * Cek apakah identitas warga sudah terverifikasi.
     */
    public function isVerified(): bool
    {
        return $this->status_verifikasi === self::STATUS_TERVERIFIKASI;
    }

    /**
     * Cek apakah identitas warga masih menunggu verifikasi.
     */
    public function isPending(): bool
    {
        return $this->status_verifikasi === self::STATUS_MENUNGGU;
    }

    /**
     * Cek apakah identitas warga ditolak.
     */
    public function isRejected(): bool
    {
        return $this->status_verifikasi === self::STATUS_DITOLAK;
    }

    public function getIsVerifiedAttribute(): bool
    {
        return $this->isVerified();
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->isPending();
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->isRejected();
    }

    /**
     * Konfigurasi label dan warna badge status verifikasi untuk UI.
     *
     * @return array{label: string, bg: string, text: string, dot: string}
     */
    public function getBadgeStatusAttribute(): array
    {
        return match ($this->status_verifikasi) {
            self::STATUS_TERVERIFIKASI => [
                'label' => 'Terverifikasi',
                'bg' => 'bg-emerald-50 border-emerald-200',
                'text' => 'text-emerald-700',
                'dot' => 'bg-emerald-600',
            ],
            self::STATUS_DITOLAK => [
                'label' => 'Ditolak',
                'bg' => 'bg-rose-50 border-rose-200',
                'text' => 'text-rose-700',
                'dot' => 'bg-rose-600',
            ],
            default => [
                'label' => 'Menunggu Verifikasi',
                'bg' => 'bg-amber-50 border-amber-200',
                'text' => 'text-amber-700',
                'dot' => 'bg-amber-500',
            ],
        };
    }
}
