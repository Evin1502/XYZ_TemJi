<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\AturanPenukaranPoin;
use App\Models\KategoriDaurUlang;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PoinWargaController extends Controller
{
    /**
     * Tampilan utama panduan kategori, saldo poin, dan riwayat transaksi poin warga.
     * Sesuai spesifikasi PBI 06 (XT-12) & Mockup-PBI-06-v2.png.
     */
    public function index(Request $request)
    {
        // 1. Resolusi Konteks Warga (Otentikasi / Query switcher / Fallback Dewi Lestari)
        $warga = null;

        if (Auth::check() && Auth::user()->warga) {
            $warga = Auth::user()->warga;
        }

        if ($request->filled('warga_id')) {
            $wargaQuery = Warga::with(['wilayah', 'riwayatPoin'])->find($request->query('warga_id'));
            if ($wargaQuery) {
                $warga = $wargaQuery;
            }
        }

        if (!$warga) {
            $warga = Warga::with(['wilayah', 'riwayatPoin'])
                ->where('nama_lengkap', 'Dewi Lestari')
                ->first()
                ?? Warga::with(['wilayah', 'riwayatPoin'])->first();
        }

        // 2. Panduan Kategori Daur Ulang Aktif (hanya is_active = true)
        $daftarKategori = KategoriDaurUlang::active()
            ->with('tarifAktif')
            ->orderBy('id')
            ->get();

        // 3. Aturan Penukaran Poin Aktif & Kalkulasi Setara Potongan Iuran (Rp)
        $aturanAktif = AturanPenukaranPoin::aktif()->first();
        $nilaiRupiahPerPoin = $aturanAktif ? (int) $aturanAktif->nilai_rupiah_per_poin : 100;
        $saldoPoin = $warga ? (int) $warga->saldo_poin : 0;
        $setaraRupiah = $saldoPoin * $nilaiRupiahPerPoin;

        // 4. Riwayat Transaksi Poin Ringkas (5 teratas) & Seluruh Riwayat untuk Modal
        $riwayatPoinRingkas = $warga
            ? $warga->riwayatPoin()->with('kategori')->latest()->take(5)->get()
            : collect();

        $riwayatPoinSemua = $warga
            ? $warga->riwayatPoin()->with('kategori')->latest()->get()
            : collect();

        // 5. Daftar Warga untuk Fitur Switcher Demo
        $daftarWargaDemo = Warga::with('wilayah')->orderBy('nama_lengkap')->get();

        return view('warga.poin.index', compact(
            'warga',
            'daftarKategori',
            'aturanAktif',
            'nilaiRupiahPerPoin',
            'saldoPoin',
            'setaraRupiah',
            'riwayatPoinRingkas',
            'riwayatPoinSemua',
            'daftarWargaDemo'
        ));
    }
}
