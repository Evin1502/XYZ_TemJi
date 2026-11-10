<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AturanPenukaranPoin extends Model
{
    use HasFactory;

    protected $table = 'aturan_penukaran_poin';

    protected $fillable = [
        'nilai_rupiah_per_poin',
        'batas_maksimal_potongan_persen',
        'minimal_poin_tukar',
        'berlaku_mulai',
        'berlaku_sampai',
        'label_periode',
        'diubah_oleh',
    ];

    protected $casts = [
        'berlaku_mulai' => 'date',
        'berlaku_sampai' => 'date',
        'nilai_rupiah_per_poin' => 'integer',
        'batas_maksimal_potongan_persen' => 'integer',
        'minimal_poin_tukar' => 'integer',
    ];

    /**
     * Admin yang menetapkan atau mengubah aturan ini.
     */
    public function diubahOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diubah_oleh');
    }

    /**
     * Scope untuk mengambil aturan penukaran yang sedang aktif saat ini.
     */
    public function scopeAktif($query)
    {
        return $query->whereNull('berlaku_sampai')
            ->orderByDesc('berlaku_mulai')
            ->orderByDesc('id');
    }

    /**
     * Scope untuk mengurutkan riwayat seluruh aturan penukaran.
     */
    public function scopeRiwayat($query)
    {
        return $query->orderByDesc('berlaku_mulai')
            ->orderByDesc('id');
    }

    /**
     * Cek apakah aturan ini merupakan aturan aktif saat ini.
     */
    public function isAktif(): bool
    {
        return is_null($this->berlaku_sampai) && $this->berlaku_mulai <= Carbon::now()->toDateString();
    }

    /**
     * Label status aturan: Berlaku, Terjadwal, atau Selesai.
     */
    public function getStatusLabelAttribute(): string
    {
        if (!is_null($this->berlaku_sampai)) {
            return 'Selesai';
        }

        if ($this->berlaku_mulai > Carbon::now()->toDateString()) {
            return 'Terjadwal';
        }

        return 'Berlaku';
    }

    /**
     * Konfigurasi badge warna status untuk Blade UI.
     *
     * @return array{label: string, bg: string, text: string, dot: string}
     */
    public function getBadgeStatusAttribute(): array
    {
        return match ($this->status_label) {
            'Berlaku' => [
                'label' => 'Berlaku',
                'bg' => 'bg-[#EBF7EE]',
                'text' => 'text-[#14532D]',
                'dot' => 'bg-[#15803D]',
            ],
            'Terjadwal' => [
                'label' => 'Terjadwal',
                'bg' => 'bg-[#FEF3C7]',
                'text' => 'text-[#92400E]',
                'dot' => 'bg-[#D97706]',
            ],
            default => [
                'label' => 'Selesai',
                'bg' => 'bg-slate-100',
                'text' => 'text-slate-600',
                'dot' => 'bg-slate-400',
            ],
        };
    }

    /**
     * Label rentang periode berlakunya aturan untuk tabel riwayat.
     */
    public function getPeriodeLabelAttribute(): string
    {
        if (!empty($this->attributes['label_periode'])) {
            return $this->attributes['label_periode'];
        }

        $mulai = $this->berlaku_mulai ? $this->berlaku_mulai->translatedFormat('j M Y') : '-';

        if (is_null($this->berlaku_sampai)) {
            return "{$mulai} - sekarang";
        }

        $sampai = $this->berlaku_sampai->translatedFormat('j M Y');
        return "{$mulai} - {$sampai}";
    }

    /**
     * Hitung simulasi potongan iuran bagi warga berdasarkan saldo poin dan total tagihan.
     *
     * @param int $nominalTagihan Total tagihan iuran bulanan (contoh: Rp 20.000)
     * @param int $saldoPoin Saldo poin daur ulang yang dimiliki warga (contoh: 180 poin)
     * @return array
     */
    public function hitungSimulasi(int $nominalTagihan = 20000, int $saldoPoin = 180): array
    {
        $nilaiSaldoRupiah = $saldoPoin * $this->nilai_rupiah_per_poin;
        $batasPotonganRupiah = (int) round(($nominalTagihan * $this->batas_maksimal_potongan_persen) / 100);

        $cukupMinimal = $saldoPoin >= $this->minimal_poin_tukar;

        if (!$cukupMinimal || $nominalTagihan <= 0 || $saldoPoin <= 0) {
            $potonganDipakai = 0;
            $poinTerpakai = 0;
        } else {
            $potonganMaksimal = min($nilaiSaldoRupiah, $batasPotonganRupiah, $nominalTagihan);
            $poinTerpakai = (int) min($saldoPoin, (int) ceil($potonganMaksimal / $this->nilai_rupiah_per_poin));
            $potonganDipakai = $poinTerpakai * $this->nilai_rupiah_per_poin;

            // Jika hasil kali pembulatan melebihi batas maksimal potongan rupiah
            if ($potonganDipakai > $batasPotonganRupiah) {
                $poinTerpakai = (int) floor($batasPotonganRupiah / $this->nilai_rupiah_per_poin);
                $potonganDipakai = $poinTerpakai * $this->nilai_rupiah_per_poin;
            }
        }

        $sisaSaldoPoin = max(0, $saldoPoin - $poinTerpakai);
        $sisaYangDibayar = max(0, $nominalTagihan - $potonganDipakai);

        $persenPotongan = $nominalTagihan > 0 ? (int) min(100, round(($potonganDipakai / $nominalTagihan) * 100)) : 0;
        $persenDibayar = 100 - $persenPotongan;

        return [
            'nominal_tagihan' => $nominalTagihan,
            'saldo_poin' => $saldoPoin,
            'nilai_saldo_rupiah' => $nilaiSaldoRupiah,
            'batas_potongan_persen' => $this->batas_maksimal_potongan_persen,
            'batas_potongan_rupiah' => $batasPotonganRupiah,
            'minimal_poin_tukar' => $this->minimal_poin_tukar,
            'cukup_minimal' => $cukupMinimal,
            'potongan_dipakai' => $potonganDipakai,
            'sisa_yang_dibayar' => $sisaYangDibayar,
            'poin_terpakai' => $poinTerpakai,
            'sisa_saldo_poin' => $sisaSaldoPoin,
            'persen_potongan' => $persenPotongan,
            'persen_dibayar' => $persenDibayar,
        ];
    }
}
