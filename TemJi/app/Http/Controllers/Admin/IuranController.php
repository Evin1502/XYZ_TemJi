<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IuranWilayah;
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
        return redirect()->back()->with('success', 'Berhasil menerbitkan 566 tagihan bulanan!');
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

        return redirect()->back()->with('success', 'Nominal iuran berhasil diperbarui!');
    }
}