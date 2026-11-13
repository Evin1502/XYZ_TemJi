<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SetujuiPenjemputanTambahanRequest;
use App\Http\Requests\TolakPenjemputanTambahanRequest;
use App\Models\JadwalPenjemputan;
use App\Models\PenjemputanTambahan;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenjemputanTambahanController extends Controller
{
    /**
     * Daftar pengajuan penjemputan tambahan beserta panel proses (setujui / tolak).
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', PenjemputanTambahan::STATUS_MENUNGGU);
        if ($status !== 'semua' && ! array_key_exists($status, PenjemputanTambahan::STATUS)) {
            $status = PenjemputanTambahan::STATUS_MENUNGGU;
        }
        $wilayahId = $request->integer('wilayah') ?: null;

        $dasar = PenjemputanTambahan::query()->when($wilayahId, fn ($q) => $q->where('wilayah_id', $wilayahId));

        $pengajuanList = (clone $dasar)
            ->with(['user.warga', 'wilayah', 'petugas'])
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->orderBy('tanggal_diminta')
            ->orderBy('id')
            ->get();

        $terpilih = $pengajuanList->firstWhere('id', $request->integer('pilih')) ?? $pengajuanList->first();

        // Acuan admin: jadwal rutin wilayah pada hari yang diminta, agar tambahan tidak menabrak rute rutin
        $jadwalRutinHariItu = $terpilih
            ? JadwalPenjemputan::with('petugas')
                ->untukWilayah($terpilih->wilayah_id)
                ->where('hari', $terpilih->tanggal_diminta->dayOfWeekIso)
                ->orderBy('jam_mulai')
                ->get()
            : collect();

        return view('admin.penjemputan_tambahan.index', [
            'status' => $status,
            'wilayahId' => $wilayahId,
            'wilayahList' => Wilayah::where('is_aktif', true)->orderBy('id')->get(),
            'jumlahPerStatus' => (clone $dasar)->selectRaw('status, count(*) as jumlah')->groupBy('status')->pluck('jumlah', 'status'),
            'pengajuanList' => $pengajuanList,
            'terpilih' => $terpilih,
            'jadwalRutinHariItu' => $jadwalRutinHariItu,
            'petugasList' => User::petugas()->with('wilayah')->orderBy('name')->get(),
        ]);
    }

    /**
     * Setujui pengajuan dan masukkan ke jadwal petugas.
     */
    public function setujui(SetujuiPenjemputanTambahanRequest $request, int $id): RedirectResponse
    {
        $pengajuan = PenjemputanTambahan::findOrFail($id);

        if (! $pengajuan->isMenunggu()) {
            return $this->sudahDiproses($pengajuan);
        }

        $pengajuan->update([
            'petugas_id' => $request->validated('petugas_id'),
            'tanggal_jemput' => $request->validated('tanggal_jemput'),
            'jam_mulai' => JadwalPenjemputan::normalisasiJam($request->validated('jam_mulai')),
            'jam_selesai' => JadwalPenjemputan::normalisasiJam($request->validated('jam_selesai')),
            'status' => PenjemputanTambahan::STATUS_DISETUJUI,
            'diproses_oleh' => $request->user()->id,
            'diproses_pada' => now(),
        ]);

        return redirect()->route('admin.penjemputan-tambahan.index')
            ->with('success', "Pengajuan {$pengajuan->kode} disetujui dan masuk ke jadwal petugas.");
    }

    /**
     * Tolak pengajuan dengan alasan yang akan dilihat warga.
     */
    public function tolak(TolakPenjemputanTambahanRequest $request, int $id): RedirectResponse
    {
        $pengajuan = PenjemputanTambahan::findOrFail($id);

        if (! $pengajuan->isMenunggu()) {
            return $this->sudahDiproses($pengajuan);
        }

        $pengajuan->update([
            'alasan_penolakan' => $request->validated('alasan_penolakan'),
            'status' => PenjemputanTambahan::STATUS_DITOLAK,
            'diproses_oleh' => $request->user()->id,
            'diproses_pada' => now(),
        ]);

        return redirect()->route('admin.penjemputan-tambahan.index')
            ->with('success', "Pengajuan {$pengajuan->kode} ditolak.");
    }

    private function sudahDiproses(PenjemputanTambahan $pengajuan): RedirectResponse
    {
        return redirect()->route('admin.penjemputan-tambahan.index')
            ->withErrors(['status' => "Pengajuan {$pengajuan->kode} sudah {$pengajuan->status_label} dan tidak dapat diproses lagi."]);
    }
}
