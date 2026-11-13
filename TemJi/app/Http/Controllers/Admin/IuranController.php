<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IuranWilayah;
use App\Models\TagihanIuran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IuranController extends Controller
{
    public function index()
    {
        $listIuran = IuranWilayah::all();
        return view('admin.iuran.index', compact('listIuran'));
    }

    public function store(Request $request)
    {
        $wilayahDipilih = $request->input('wilayah', []);

        $daftarWilayah = !empty($wilayahDipilih)
            ? IuranWilayah::whereIn('wilayah', $wilayahDipilih)->get()
            : IuranWilayah::all();

        // Memastikan format jatuh_tempo valid untuk kolom DATE MySQL
        try {
            $jatuhTempo = $request->filled('jatuh_tempo')
                ? Carbon::parse($request->jatuh_tempo)->format('Y-m-d')
                : '2026-11-10';
        } catch (\Exception $e) {
            $jatuhTempo = '2026-11-10';
        }

        foreach ($daftarWilayah as $item) {
            TagihanIuran::create([
                'nama_warga'     => Auth::check() ? Auth::user()->name : 'Warga ' . $item->wilayah,
                'wilayah'        => $item->wilayah,
                'bulan_tagihan'  => $request->bulan_tagihan ?? 'November 2026',
                'nominal_asal'   => $item->nominal,
                'poin_digunakan' => 0,
                'potongan_poin'  => 0,
                'total_dibayar'  => $item->nominal,
                'jatuh_tempo'    => $jatuhTempo,
                'status'         => 'Belum dibayar',
            ]);
        }

        return redirect()->back()->with('success', 'Berhasil menerbitkan tagihan iuran bulanan untuk semua wilayah terpilih!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required|numeric',
        ]);

        $iuran = IuranWilayah::findOrFail($id);
        $iuran->update([
            'nominal' => $request->nominal,
            'berlaku_sejak' => now(),
        ]);

        return redirect()->back()->with('success', 'Nominal iuran wilayah berhasil diperbarui!');
    }
}