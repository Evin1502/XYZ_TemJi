<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VerifikasiWargaController extends Controller
{
    /**
     * Tampilkan daftar antrean verifikasi identitas warga di Panel Admin TemJi.
     */
    public function index(Request $request): View
    {
        return $this->renderPanel($request, $request->filled('selected') ? (int) $request->query('selected') : null);
    }

    /**
     * Tampilkan detail spesifik pengajuan warga pada panel verifikasi.
     */
    public function show(Request $request, int $id): View
    {
        Warga::findOrFail($id);

        return $this->renderPanel($request, $id);
    }

    /**
     * Setujui verifikasi identitas akun warga.
     */
    public function approve(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'catatan_verifikasi' => ['nullable', 'string', 'max:500'],
        ]);

        $warga = Warga::findOrFail($id);

        DB::transaction(function () use ($warga, $request) {
            $verifikatorId = $this->resolveAdminId();

            $warga->update([
                'status_verifikasi' => Warga::STATUS_TERVERIFIKASI,
                'catatan_verifikasi' => $request->filled('catatan_verifikasi')
                    ? $request->catatan_verifikasi
                    : 'Identitas KTP/KK telah diverifikasi sesuai domisili wilayah.',
                'diverifikasi_oleh' => $verifikatorId,
                'diverifikasi_pada' => Carbon::now(),
            ]);
        });

        return redirect()
            ->route('admin.verifikasi-warga.index', ['selected' => $warga->id])
            ->with('success', "Identitas warga {$warga->nama_lengkap} berhasil diverifikasi.");
    }

    /**
     * Tolak verifikasi identitas akun warga dengan alasan penolakan.
     */
    public function reject(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'catatan_verifikasi' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'catatan_verifikasi.required' => 'Alasan penolakan wajib diisi agar warga mengetahui perbaikan yang diperlukan.',
            'catatan_verifikasi.min' => 'Alasan penolakan minimal 5 karakter.',
            'catatan_verifikasi.max' => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $warga = Warga::findOrFail($id);

        DB::transaction(function () use ($warga, $request) {
            $verifikatorId = $this->resolveAdminId();

            $warga->update([
                'status_verifikasi' => Warga::STATUS_DITOLAK,
                'catatan_verifikasi' => $request->catatan_verifikasi,
                'diverifikasi_oleh' => $verifikatorId,
                'diverifikasi_pada' => Carbon::now(),
            ]);
        });

        return redirect()
            ->route('admin.verifikasi-warga.index', ['selected' => $warga->id])
            ->with('success', "Pengajuan identitas {$warga->nama_lengkap} telah ditolak beserta catatan perbaikan.");
    }

    /**
     * Siapkan data tabel, statistik, dan panel kanan verifikasi.
     */
    private function renderPanel(Request $request, ?int $selectedId = null): View
    {
        $statusFilter = $request->query('status', 'semua');
        $wilayahFilter = $request->query('wilayah_id');
        $search = trim((string) $request->query('q', ''));

        $totalCount = Warga::count();
        $menungguCount = Warga::menunggu()->count();
        $terverifikasiCount = Warga::terverifikasi()->count();
        $ditolakCount = Warga::ditolak()->count();

        $query = Warga::with(['wilayah', 'user', 'verifikator']);

        if (in_array($statusFilter, [Warga::STATUS_MENUNGGU, Warga::STATUS_TERVERIFIKASI, Warga::STATUS_DITOLAK], true)) {
            $query->where('status_verifikasi', $statusFilter);
        }

        if ($wilayahFilter) {
            $query->where('wilayah_id', $wilayahFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('no_kk', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('email', 'like', "%{$search}%"));
            });
        }

        $wargaList = $query
            ->orderByRaw("CASE WHEN status_verifikasi = 'menunggu' THEN 0 WHEN status_verifikasi = 'ditolak' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->get();

        $selectedWarga = null;
        if ($selectedId) {
            $selectedWarga = Warga::with(['wilayah', 'user', 'verifikator'])->find($selectedId);
        }
        if (!$selectedWarga && $wargaList->isNotEmpty()) {
            $selectedWarga = $wargaList->first();
        }

        $wilayahList = Wilayah::aktif()->orderBy('nama')->get();

        return view('admin.verifikasi_warga.index', compact(
            'wargaList',
            'selectedWarga',
            'wilayahList',
            'statusFilter',
            'wilayahFilter',
            'search',
            'totalCount',
            'menungguCount',
            'terverifikasiCount',
            'ditolakCount'
        ));
    }

    /**
     * Tentukan ID admin verifikator dari sesi admin yang sedang login.
     */
    private function resolveAdminId(): ?int
    {
        return Auth::check() && Auth::user()->isAdmin() ? Auth::id() : null;
    }
}
