<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrasiWargaRequest;
use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RegistrasiWargaController extends Controller
{
    /**
     * Tampilkan halaman formulir registrasi akun warga.
     */
    public function showRegistrationForm(): View
    {
        $wilayahList = Wilayah::aktif()->orderBy('nama')->get();

        return view('auth.registrasi_warga', compact('wilayahList'));
    }

    /**
     * Proses pendaftaran akun warga baru beserta unggah bukti identitas.
     */
    public function register(RegistrasiWargaRequest $request): RedirectResponse
    {
        $fotoPath = $request->file('foto_identitas')->store('identitas_warga', 'public');

        $warga = DB::transaction(function () use ($request, $fotoPath) {
            $user = User::create([
                'name' => $request->nama_lengkap,
                'email' => $request->email,
                'role' => 'warga',
                'password' => $request->password,
            ]);

            return Warga::create([
                'user_id' => $user->id,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'nama_lengkap' => $request->nama_lengkap,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'wilayah_id' => $request->wilayah_id,
                'foto_identitas' => $fotoPath,
                'status_verifikasi' => Warga::STATUS_MENUNGGU,
            ]);
        });

        Auth::login($warga->user);
        session(['registered_warga_id' => $warga->id]);

        return redirect()
            ->route('warga.registrasi.status', ['warga' => $warga->id])
            ->with('success', 'Registrasi berhasil dikirim! Identitas Anda sedang dalam antrean verifikasi Admin TemJi.');
    }

    /**
     * Status pendaftaran: hanya milik akun yang sedang login.
     * Pencarian ?nik= / ?warga= dihapus karena membuka data warga lain.
     */
    public function statusPendaftaran(Request $request): View
    {
        $warga = $request->user()->warga()
            ->with(['wilayah', 'user', 'verifikator'])
            ->firstOrFail();

        // Dipertahankan agar blade lama tidak error (blok pencarian sudah dihapus dari blade).
        $searchedNik = '';
        $daftarWargaTerbaru = collect();

        return view('auth.status_pendaftaran', compact('warga', 'searchedNik', 'daftarWargaTerbaru'));
    }

    /**
     * Ajukan ulang perbaikan dokumen identitas bagi warga yang sebelumnya ditolak.
     */
    public function resubmit(Request $request, int $id): RedirectResponse
    {
        // Hanya pemilik data, dan hanya bila status ditolak.
        $warga = $request->user()->warga;
        abort_unless($warga && $warga->id === $id, 403);
        abort_unless($warga->isRejected(), 403);

        $request->validate([
            'foto_identitas' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'no_hp' => ['nullable', 'string', 'min:10', 'max:15'],
        ], [
            'foto_identitas.required' => 'Foto bukti identitas baru wajib diunggah.',
            'foto_identitas.image' => 'Berkas bukti identitas harus berupa gambar.',
            'foto_identitas.mimes' => 'Format foto harus berupa JPG, JPEG, atau PNG.',
            'foto_identitas.max' => 'Ukuran berkas maksimal 2 MB.',
        ]);

        if ($warga->foto_identitas && !str_starts_with($warga->foto_identitas, 'identitas_warga/sample_')) {
            Storage::disk('public')->delete($warga->foto_identitas);
        }

        $newFotoPath = $request->file('foto_identitas')->store('identitas_warga', 'public');

        $warga->update([
            'foto_identitas' => $newFotoPath,
            'alamat' => $request->filled('alamat') ? $request->alamat : $warga->alamat,
            'no_hp' => $request->filled('no_hp') ? $request->no_hp : $warga->no_hp,
            'status_verifikasi' => Warga::STATUS_MENUNGGU,
            'catatan_verifikasi' => null,
            'diverifikasi_oleh' => null,
            'diverifikasi_pada' => null,
        ]);

        return redirect()
            ->route('warga.registrasi.status', ['warga' => $warga->id])
            ->with('success', 'Dokumen perbaikan berhasil diunggah ulang dan kembali menunggu verifikasi Admin.');
    }
}
