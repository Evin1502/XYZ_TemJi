<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\TagihanIuran;
use Illuminate\Http\Request;

class IuranController extends Controller
{
    public function index()
    {
        try {
            // Ambil tagihan aktif yang belum dibayar
            $tagihanAktif = TagihanIuran::where('status', 'Belum dibayar')->first();
            // Ambil riwayat tagihan yang sudah lunas
            $riwayatTagihan = TagihanIuran::where('status', 'Lunas')->latest()->get();
        } catch (\Exception $e) {
            $tagihanAktif = null;
            $riwayatTagihan = collect();
        }

        return view('warga.iuran.index', compact('tagihanAktif', 'riwayatTagihan'));
    }

    public function bayar(Request $request, $id)
    {
        // Validasi: Bukti transfer WAJIB dikirim jika memilih metode transfer
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

            // Logika diskon poin
            $pakaiPoin = $request->boolean('pakai_poin') || $request->input('pakai_poin') == '1' || $request->input('pakai_poin') == 'on';
            $poinDigunakan = $pakaiPoin ? 100 : 0;
            $potonganPoin = $pakaiPoin ? 10000 : 0;
            $totalDibayar = max(0, $tagihan->nominal_asal - $potonganPoin);

            // Simpan file bukti transfer jika metode transfer dipilih
            $pathBukti = null;
            if ($request->metode_pembayaran === 'transfer' && $request->hasFile('bukti_transfer')) {
                $pathBukti = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
            }

            // Update status tagihan ke Lunas
            $tagihan->update([
                'metode_pembayaran' => $request->metode_pembayaran,
                'bukti_transfer'    => $pathBukti,
                'poin_digunakan'    => $poinDigunakan,
                'potongan_poin'     => $potonganPoin,
                'total_dibayar'     => $totalDibayar,
                'status'            => 'Lunas',
            ]);

            return redirect()->back()->with('success', 'Pembayaran iuran berhasil dikirim dan diverifikasi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses pembayaran iuran: ' . $e->getMessage());
        }
    }
}
