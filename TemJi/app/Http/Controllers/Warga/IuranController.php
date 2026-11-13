<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\TagihanIuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IuranController extends Controller
{
    public function index(Request $request)
    {
        try {
            // 1. Ambil wilayah dari user yang sedang login, atau parameter query ?wilayah= saat testing tanpa login
            $wilayahWarga = Auth::check() ? Auth::user()->wilayah : $request->query('wilayah');

            // 2. Query dinamis menyesuaikan wilayah warga
            $queryAktif = TagihanIuran::where('status', 'Belum dibayar');
            $queryRiwayat = TagihanIuran::where('status', 'Lunas');

            if ($wilayahWarga) {
                $queryAktif->where('wilayah', $wilayahWarga);
                $queryRiwayat->where('wilayah', $wilayahWarga);
            }

            $tagihanAktif = $queryAktif->latest()->first();
            $riwayatTagihan = $queryRiwayat->latest()->get();
        } catch (\Exception $e) {
            $tagihanAktif = null;
            $riwayatTagihan = collect();
        }

        return view('warga.iuran.index', compact('tagihanAktif', 'riwayatTagihan'));
    }

    public function bayar(Request $request, $id)
    {
        $request->validate([
            'metode_pembayaran' => 'required|in:transfer,tunai',
            'bukti_transfer' => 'required_if:metode_pembayaran,transfer|nullable|image|mimes:jpeg,png,jpg|max:5120',
        ], [
            'bukti_transfer.required_if' => 'Silakan unggah bukti transfer terlebih dahulu!',
            'bukti_transfer.image' => 'Bukti transfer harus berupa gambar (JPG/PNG).',
            'bukti_transfer.max' => 'Ukuran file bukti transfer maksimal 5 MB.',
        ]);

        try {
            $tagihan = TagihanIuran::findOrFail($id);

            // Logika diskon poin dinamis (maksimal Rp 10.000 atau 50% dari nominal asal)
            $pakaiPoin = $request->has('pakai_poin');
            $poinDigunakan = $pakaiPoin ? 100 : 0;
            $potonganPoin = $pakaiPoin ? min(10000, $tagihan->nominal_asal * 0.5) : 0;
            $totalDibayar = max(0, $tagihan->nominal_asal - $potonganPoin);

            $pathBukti = null;
            if ($request->metode_pembayaran === 'transfer' && $request->hasFile('bukti_transfer')) {
                $pathBukti = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
            }

            $tagihan->update([
                'poin_digunakan' => $poinDigunakan,
                'potongan_poin'  => $potonganPoin,
                'total_dibayar'  => $totalDibayar,
                'bukti_transfer' => $pathBukti,
                'status'         => 'Lunas',
            ]);

            return redirect()->back()->with('success', 'Pembayaran iuran berhasil dikirim dan diverifikasi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran iuran: ' . $e->getMessage());
        }
    }
}