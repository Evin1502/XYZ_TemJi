<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nilai_rupiah_per_poin' => ['required', 'integer', 'min:1'],
            'batas_maksimal_potongan_persen' => ['required', 'integer', 'min:0', 'max:100'],
            'minimal_poin_tukar' => ['required', 'integer', 'min:0'],
            'berlaku_mulai' => ['required'],
            'label_periode' => ['nullable', 'string', 'max:100'],
        ], [
            'nilai_rupiah_per_poin.required' => 'Nilai rupiah per 1 poin wajib diisi.',
            'nilai_rupiah_per_poin.min' => 'Nilai rupiah per 1 poin minimal Rp 1.',
            'batas_maksimal_potongan_persen.required' => 'Batas maksimal potongan wajib diisi.',
            'batas_maksimal_potongan_persen.min' => 'Batas maksimal potongan tidak boleh bernilai negatif.',
            'batas_maksimal_potongan_persen.max' => 'Batas maksimal potongan tidak boleh melebihi 100%.',
            'minimal_poin_tukar.required' => 'Minimal poin untuk ditukarkan wajib diisi.',
            'minimal_poin_tukar.min' => 'Minimal poin tidak boleh bernilai negatif.',
            'berlaku_mulai.required' => 'Tanggal atau periode mulai berlaku wajib ditentukan.',
        ]);

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
