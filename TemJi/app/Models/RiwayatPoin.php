<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiwayatPoin extends Model
{
    use HasFactory;

    protected $table = 'riwayat_poin';

    protected $fillable = [
        'warga_id',
        'jenis',
        'jumlah_poin',
        'saldo_akhir',
        'judul',
        'keterangan',
        'kode_pengajuan',
        'kategori_id',
        'berat_kg',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'jumlah_poin' => 'integer',
        'saldo_akhir' => 'integer',
        'berat_kg' => 'float',
    ];

    /**
     * Warga pemilik transaksi poin ini.
     */
    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class, 'warga_id');
    }

    /**
     * Kategori daur ulang jika transaksi berasal dari setoran sampah berpoin.
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriDaurUlang::class, 'kategori_id');
    }

    /**
     * Scope untuk transaksi poin masuk (reward daur ulang).
     */
    public function scopeMasuk($query)
    {
        return $query->where('jenis', 'masuk');
    }

    /**
     * Scope untuk transaksi poin keluar (potongan iuran).
     */
    public function scopeKeluar($query)
    {
        return $query->where('jenis', 'keluar');
    }

    /**
     * Tanda plus (+) untuk poin masuk dan minus (-) untuk poin keluar.
     */
    public function getBadgeSignAttribute(): string
    {
        return $this->jenis === 'masuk' ? '+' : '-';
    }

    /**
     * Warna teks tampilan: Hijau (#16A34A) untuk masuk, Merah (#DC2626) untuk keluar.
     */
    public function getBadgeColorAttribute(): string
    {
        return $this->jenis === 'masuk' ? 'text-[#16A34A]' : 'text-[#DC2626]';
    }

    /**
     * Format nilai poin lengkap dengan tanda, contoh: "+24" atau "-100".
     */
    public function getFormattedPoinAttribute(): string
    {
        return ($this->jenis === 'masuk' ? '+' : '-') . $this->jumlah_poin;
    }
}
