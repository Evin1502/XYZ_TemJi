<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenjemputanTambahanRequest;
use App\Models\JadwalPenjemputan;
use App\Models\PenjemputanTambahan;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalWargaController extends Controller
{
    /**
     * Jadwal penjemputan rutin di wilayah warga beserta pengajuan penjemputan tambahan miliknya.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $wilayahId = $user->wilayah_id_efektif;

        // Tanpa wilayah tidak ada jadwal yang relevan (jangan tampilkan jadwal semua wilayah).
        $jadwalPerHari = $wilayahId
            ? JadwalPenjemputan::with('petugas')
                ->untukWilayah($wilayahId)
                ->orderBy('jam_mulai')
                ->get()
                ->groupBy('hari')
            : collect();

        $riwayat = $user->penjemputanTambahan()->with('petugas')->latest('id')->get();

        return view('warga.jadwal.index', [
            'wilayah' => $wilayahId ? Wilayah::find($wilayahId) : null,
            'jadwalPerHari' => $jadwalPerHari,
            'riwayat' => $riwayat,
            'adaMenunggu' => $riwayat->contains(fn (PenjemputanTambahan $p) => $p->isMenunggu()),
            'tanggalMinimal' => StorePenjemputanTambahanRequest::tanggalMinimal(),
            'tanggalMaksimal' => StorePenjemputanTambahanRequest::tanggalMaksimal(),
        ]);
    }

    /**
     * Simpan pengajuan penjemputan tambahan untuk wilayah warga yang login.
     */
    public function store(StorePenjemputanTambahanRequest $request): RedirectResponse
    {
        $user = $request->user();
        $wilayahId = $user->wilayah_id_efektif;

        if (! $wilayahId) {
            return redirect()->route('warga.jadwal.index')
                ->withInput()
                ->withErrors(['wilayah' => 'Wilayah Anda belum lengkap. Hubungi admin untuk melengkapi wilayah sebelum mengajukan penjemputan tambahan.']);
        }

        $pengajuan = PenjemputanTambahan::create([
            'kode' => PenjemputanTambahan::generateKode(),
            'user_id' => $user->id,
            'wilayah_id' => $wilayahId,
            'tanggal_diminta' => $request->validated('tanggal_diminta'),
            'alasan' => $request->validated('alasan'),
            'status' => PenjemputanTambahan::STATUS_MENUNGGU,
        ]);

        return redirect()->route('warga.jadwal.index')
            ->with('success', "Pengajuan {$pengajuan->kode} terkirim dan menunggu persetujuan admin.");
    }

    /**
     * Batalkan pengajuan milik sendiri yang belum diproses admin.
     */
    public function batal(Request $request, int $id): RedirectResponse
    {
        // Pengajuan warga lain -> 404
        $pengajuan = $request->user()->penjemputanTambahan()->findOrFail($id);

        if (! $pengajuan->isMenunggu()) {
            return redirect()->route('warga.jadwal.index')
                ->with('error', 'Pengajuan yang sudah diproses tidak dapat dibatalkan.');
        }

        $pengajuan->update(['status' => PenjemputanTambahan::STATUS_DIBATALKAN]);

        return redirect()->route('warga.jadwal.index')
            ->with('success', "Pengajuan {$pengajuan->kode} dibatalkan.");
    }
}
