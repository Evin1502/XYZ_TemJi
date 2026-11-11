<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePengajuanDaurUlangRequest;
use App\Models\KategoriDaurUlang;
use App\Models\PengajuanDaurUlang;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengajuanDaurUlangController extends Controller
{
    /**
     * Nama kategori yang dipilih secara default pada formulir,
     * beserta perkiraan beratnya (kg). Menyerupai mockup UI.
     */
    private const DEFAULT_KATEGORI_TERPILIH = [
        'Minyak jelantah' => 2,
        'Botol PET bening' => 3,
    ];

    /**
     * Berat awal (kg) untuk kategori yang belum dipilih.
     */
    private const DEFAULT_BERAT_KG = 1;

    /**
     * Catatan bawaan jika warga tidak mengisi catatan.
     */
    private const DEFAULT_CATATAN = 'Pastikan sudah dipisah per kategori';

    /**
     * Tampilkan formulir pengajuan daur ulang warga.
     */
    public function create(Request $request): View
    {
        $kategoriList = KategoriDaurUlang::active()
            ->with('tarifAktif')
            ->orderBy('id')
            ->get();

        $kode = $request->query('kode') ?? session('submitted_kode');
        $pengajuanTerkirim = null;

        if ($kode) {
            $pengajuanTerkirim = PengajuanDaurUlang::with(['items.kategori', 'wilayah'])
                ->where('kode_pengajuan', $kode)
                ->first();
        }

        $defaultSelection = $pengajuanTerkirim
            ? $this->selectionDariPengajuan($pengajuanTerkirim)
            : $this->selectionAwal($kategoriList);

        return view('warga.daur-ulang.create', compact('kategoriList', 'pengajuanTerkirim', 'defaultSelection'));
    }

    /**
     * Simpan pengajuan daur ulang baru.
     */
    public function store(StorePengajuanDaurUlangRequest $request): RedirectResponse
    {
        $selectedItems = $request->input('selected_items', []);

        $pengajuan = DB::transaction(function () use ($request, $selectedItems) {
            $user = Auth::user() ?? User::where('role', 'warga')->first();
            $wilayahId = $user?->wilayah_id ?? Wilayah::where('nama', 'Sukapura')->value('id');

            $totalKg = 0;
            $totalPoin = 0;
            $itemsData = [];

            foreach ($selectedItems as $item) {
                $kategori = KategoriDaurUlang::with('tarifAktif')->findOrFail($item['kategori_id']);
                $berat = (int) $item['berat_kg'];
                $poinPerKg = (int) $kategori->poin_saat_ini;
                $estimasiPoin = $berat * $poinPerKg;

                $totalKg += $berat;
                $totalPoin += $estimasiPoin;

                $itemsData[] = [
                    'kategori_id' => $kategori->id,
                    'estimasi_berat_kg' => $berat,
                    'poin_per_kg_snapshot' => $poinPerKg,
                    'estimasi_poin' => $estimasiPoin,
                ];
            }

            $pengajuanBaru = PengajuanDaurUlang::create([
                'kode_pengajuan' => PengajuanDaurUlang::generateKodePengajuan(),
                'user_id' => $user?->id,
                'wilayah_id' => $wilayahId,
                'total_estimasi_kg' => $totalKg,
                'total_estimasi_poin' => $totalPoin,
                'status' => PengajuanDaurUlang::STATUS_MENUNGGU_JADWAL,
                'catatan' => $request->input('catatan', self::DEFAULT_CATATAN),
            ]);

            $pengajuanBaru->items()->createMany($itemsData);

            return $pengajuanBaru;
        });

        return redirect()->route('warga.daur-ulang.create', ['kode' => $pengajuan->kode_pengajuan])
            ->with('success', 'Pengajuan terkirim')
            ->with('submitted_kode', $pengajuan->kode_pengajuan);
    }

    /**
     * Halaman lanjutan untuk memilih jadwal penjemputan.
     */
    public function pilihJadwal(string $kode): View
    {
        $pengajuan = PengajuanDaurUlang::with(['items.kategori', 'wilayah'])
            ->where('kode_pengajuan', $kode)
            ->firstOrFail();

        return view('warga.daur-ulang.jadwal', compact('pengajuan'));
    }

    /**
     * Bentuk state kartu terpilih berdasarkan pengajuan yang sudah terkirim.
     */
    private function selectionDariPengajuan(PengajuanDaurUlang $pengajuan): array
    {
        $selection = [];

        foreach ($pengajuan->items as $item) {
            $selection[$item->kategori_id] = [
                'selected' => true,
                'berat_kg' => $item->estimasi_berat_kg,
            ];
        }

        return $selection;
    }

    /**
     * Bentuk state awal kartu terpilih untuk formulir baru.
     */
    private function selectionAwal($kategoriList): array
    {
        $selection = [];

        foreach ($kategoriList as $kategori) {
            $beratDefault = self::DEFAULT_KATEGORI_TERPILIH[$kategori->nama] ?? null;

            $selection[$kategori->id] = [
                'selected' => $beratDefault !== null,
                'berat_kg' => $beratDefault ?? self::DEFAULT_BERAT_KG,
            ];
        }

        return $selection;
    }
}
