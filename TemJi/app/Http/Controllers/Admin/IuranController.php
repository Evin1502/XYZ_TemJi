<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IuranWilayah;
use App\Models\TagihanIuran;
use Illuminate\Http\Request;

class IuranController extends Controller
{
    public function index()
    {
        $listIuran = IuranWilayah::all();
        return view('admin.iuran.index', compact('listIuran'));
    }

    public function store(Request $request)
    {
        // 1. Ambil data nominal iuran wilayah Sukapura dari PBI 13
        $iuranSukapura = IuranWilayah::where('wilayah', 'Sukapura')->first();
        $nominal = $iuranSukapura ? $iuranSukapura->nominal : 20000;

        // 2. Terbitkan tagihan baru ke tabel PBI 14 (Sisi Warga)
        TagihanIuran::create([
            'nama_warga'     => 'Dewi Lestari',
            'wilayah'        => 'Sukapura',
            'bulan_tagihan'  => $request->input('bulan', 'November 2026'),
            'nominal_asal'   => $nominal,
            'poin_digunakan' => 0,
            'potongan_poin'  => 0,
            'total_dibayar'  => $nominal,
            'jatuh_tempo'    => '2026-11-10',
            'status'         => 'Belum dibayar',
        ]);

        return redirect()->back()->with('success', 'Berhasil menerbitkan tagihan iuran bulanan untuk warga!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:0',
        ]);

        $iuran = IuranWilayah::findOrFail($id);
        $iuran->update([
            'nominal'       => $request->nominal,
            'berlaku_sejak' => now(),
        ]);

        return redirect()->back()->with('success', 'Nominal iuran wilayah berhasil diperbarui!');
    }
}
