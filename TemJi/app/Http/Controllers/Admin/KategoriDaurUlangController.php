<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\JadwalkanTarifRequest;
use App\Http\Requests\StoreKategoriRequest;
use App\Models\KategoriDaurUlang;
use App\Models\RiwayatTarifPoin;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class KategoriDaurUlangController extends Controller
{
    /**
     * Tampilkan halaman utama kelola kategori daur ulang & riwayat tarif.
     */
    public function index(Request $request): View
    {
        $kategoriList = KategoriDaurUlang::with(['tarifAktif', 'riwayatTarif'])->get();

        $totalCount = $kategoriList->count();
        $activeCount = $kategoriList->where('is_active', true)->count();
        $inactiveCount = $kategoriList->where('is_active', false)->count();

        // Kategori yang sedang dipilih untuk panel kanan (ubah tarif & histori)
        $selectedId = $request->query('selected');
        $selectedKategori = $selectedId
            ? $kategoriList->firstWhere('id', $selectedId)
            : $kategoriList->first();

        // Jika ID tidak ditemukan, fallback ke item pertama
        if (!$selectedKategori && $kategoriList->isNotEmpty()) {
            $selectedKategori = $kategoriList->first();
        }

        return view('admin.kategori.index', compact(
            'kategoriList',
            'totalCount',
            'activeCount',
            'inactiveCount',
            'selectedKategori'
        ));
    }

    /**
     * Simpan kategori daur ulang baru beserta tarif perdana.
     */
    public function store(StoreKategoriRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $kategori = KategoriDaurUlang::create([
                'nama' => $request->nama,
                'tipe' => $request->tipe,
                'satuan' => $request->satuan ?? 'kg',
                'icon' => $request->icon ?? ($request->tipe === 'Minyak jelantah' ? 'droplet' : 'bottle'),
                'deskripsi' => $request->deskripsi,
                'is_active' => true,
            ]);

            RiwayatTarifPoin::create([
                'kategori_id' => $kategori->id,
                'poin_per_kg' => $request->poin_per_kg,
                'berlaku_sejak' => $request->filled('berlaku_sejak') ? Carbon::parse($request->berlaku_sejak) : Carbon::now(),
                'berlaku_hingga' => null,
                'keterangan' => 'Tarif awal saat penambahan kategori',
            ]);
        });

        return redirect()->route('admin.kategori.index')
            ->with('success', 'Kategori baru berhasil ditambahkan beserta tarif awalnya.');
    }

    /**
     * Perbarui data informasi kategori.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tipe' => 'required|string|max:50',
            'satuan' => 'required|string|max:20',
            'icon' => 'nullable|string|in:droplet,bottle,cup',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        $kategori = KategoriDaurUlang::findOrFail($id);
        $kategori->update($request->only(['nama', 'tipe', 'satuan', 'icon', 'deskripsi']));

        return redirect()->route('admin.kategori.index', ['selected' => $kategori->id])
            ->with('success', 'Data kategori ' . $kategori->nama . ' berhasil diperbarui.');
    }

    /**
     * Aktifkan atau nonaktifkan status kategori.
     */
    public function toggleStatus(int $id): RedirectResponse
    {
        $kategori = KategoriDaurUlang::findOrFail($id);
        $kategori->is_active = !$kategori->is_active;
        $kategori->save();

        $statusText = $kategori->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.kategori.index', ['selected' => $kategori->id])
            ->with('success', "Kategori {$kategori->nama} berhasil {$statusText}.");
    }

    /**
     * Jadwalkan tarif poin baru untuk kategori tertentu.
     */
    public function jadwalkanTarif(JadwalkanTarifRequest $request, int $id): RedirectResponse
    {
        $kategori = KategoriDaurUlang::findOrFail($id);
        $berlakuMulai = Carbon::parse($request->berlaku_mulai);

        DB::transaction(function () use ($kategori, $request, $berlakuMulai) {
            // Tutup tarif yang saat ini sedang aktif atau bertumpuk dengan tanggal baru
            $tarifAktif = RiwayatTarifPoin::where('kategori_id', $kategori->id)
                ->where(function ($q) use ($berlakuMulai) {
                    $q->whereNull('berlaku_hingga')
                      ->orWhere('berlaku_hingga', '>', $berlakuMulai);
                })
                ->where('berlaku_sejak', '<', $berlakuMulai)
                ->orderByDesc('berlaku_sejak')
                ->first();

            if ($tarifAktif) {
                $tarifAktif->update([
                    'berlaku_hingga' => $berlakuMulai->copy()->subSecond(),
                ]);
            }

            // Buat record tarif baru
            RiwayatTarifPoin::create([
                'kategori_id' => $kategori->id,
                'poin_per_kg' => $request->poin_per_kg,
                'berlaku_sejak' => $berlakuMulai,
                'berlaku_hingga' => null,
                'keterangan' => $request->keterangan ?? 'Penyesuaian tarif poin baru',
            ]);
        });

        return redirect()->route('admin.kategori.index', ['selected' => $kategori->id])
            ->with('success', "Tarif baru ({$request->poin_per_kg} poin/kg) untuk {$kategori->nama} berhasil dijadwalkan.");
    }
}
