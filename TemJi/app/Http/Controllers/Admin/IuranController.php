<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IuranWilayah;
use Illuminate\Http\Request;

class IuranController extends Controller
{
    public function index()
    {
        try {
            $listIuran = IuranWilayah::all();
        } catch (\Exception $e) {
            $listIuran = collect(); // Mengembalikan koleksi kosong jika tabel belum ada
        }

        return view('admin.iuran.index', compact('listIuran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wilayah' => 'required|string',
            'nominal' => 'required|numeric',
            'berlaku_sejak' => 'nullable|date',
        ]);

        try {
            IuranWilayah::create($request->all());
            return redirect()->back()->with('success', 'Data nominal iuran berhasil disimpan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan. Pastikan tabel database sudah ter-migrate.');
        }
    }
}