<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAturanPenukaranRequest;
use App\Models\AturanPenukaranPoin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AturanPenukaranPoinController extends Controller
{
    /**
     * Tampilkan halaman pengelolaan aturan penukaran poin dan batas potongan iuran.
     */
    public function index(): View
    {
        $aturanAktif = AturanPenukaranPoin::aktif()->first();

        // Fallback default jika database masih kosong
        if (!$aturanAktif) {
            $aturanAktif = new AturanPenukaranPoin([
                'nilai_rupiah_per_poin' => 100,
                'batas_maksimal_potongan_persen' => 50,
                'minimal_poin_tukar' => 50,
                'berlaku_mulai' => Carbon::now()->startOfMonth(),
                'berlaku_sampai' => null,
                'label_periode' => '1 Sep 2026 - sekarang',
            ]);
        }

        $riwayatAturan = AturanPenukaranPoin::riwayat()->get();

        // Parameter dasar simulasi warga (contoh tagihan Sukapura & saldo 180 poin sesuai mockup)
        $tagihanContoh = 20000;
        $saldoPoinContoh = 180;
        $simulasi = $aturanAktif->hitungSimulasi($tagihanContoh, $saldoPoinContoh);

        return view('admin.aturan_penukaran.index', compact(
            'aturanAktif',
            'riwayatAturan',
            'simulasi',
            'tagihanContoh',
            'saldoPoinContoh'
        ));
    }

    /**
     * Simpan aturan penukaran poin baru dengan mekanisme Effective Dating (SCD Type 2).
     */
    public function store(StoreAturanPenukaranRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $adminId = $this->resolveAdminId();

            // Konversi tanggal berlaku mulai jika dalam format tanggal atau string bulan
            $inputMulai = $request->input('berlaku_mulai');
            try {
                $berlakuMulai = Carbon::parse($inputMulai);
            } catch (\Exception $e) {
                $berlakuMulai = Carbon::now()->addMonth()->startOfMonth();
            }

            // Tutup aturan aktif sebelumnya (SCD Type 2)
            $aturanLama = AturanPenukaranPoin::whereNull('berlaku_sampai')->first();
            if ($aturanLama) {
                $tanggalTutup = $berlakuMulai->copy()->subDay();
                if ($tanggalTutup < $aturanLama->berlaku_mulai) {
                    $tanggalTutup = Carbon::now();
                }

                $aturanLama->update([
                    'berlaku_sampai' => $tanggalTutup,
                    'label_periode' => $aturanLama->berlaku_mulai->translatedFormat('j M') . ' - ' . $tanggalTutup->translatedFormat('j M Y'),
                ]);
            }

            // Simpan aturan baru
            $labelPeriode = $request->filled('label_periode')
                ? $request->label_periode
                : ($berlakuMulai <= Carbon::now() ? $berlakuMulai->translatedFormat('j M Y') . ' - sekarang' : 'Tagihan ' . $berlakuMulai->translatedFormat('F Y'));

            AturanPenukaranPoin::create([
                'nilai_rupiah_per_poin' => (int) $request->nilai_rupiah_per_poin,
                'batas_maksimal_potongan_persen' => (int) $request->batas_maksimal_potongan_persen,
                'minimal_poin_tukar' => (int) $request->minimal_poin_tukar,
                'berlaku_mulai' => $berlakuMulai->toDateString(),
                'berlaku_sampai' => null,
                'label_periode' => $labelPeriode,
                'diubah_oleh' => $adminId,
            ]);
        });

        return redirect()
            ->route('admin.aturan-penukaran.index')
            ->with('success', 'Aturan penukaran poin baru berhasil disimpan. Riwayat aturan lama tetap terarsip aman.');
    }

    /**
     * Tentukan ID pengguna admin verifikator.
     */
    private function resolveAdminId(): ?int
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return Auth::id();
        }

        return User::where('role', 'admin')->value('id') ?? Auth::id();
    }
}
