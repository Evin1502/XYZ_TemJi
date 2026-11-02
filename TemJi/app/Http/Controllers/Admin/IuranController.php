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
        $request->validate([
            'wilayah' => 'required|string',
            'nominal' => 'required|numeric',
            'berlaku_sejak' => 'nullable|date',
        ]);

        IuranWilayah::updateOrCreate(
            ['wilayah' => $request->wilayah],
            [
                'nominal' => $request->nominal,
                'berlaku_sejak' => $request->berlaku_sejak ?? now(),
            ]
        );

        return redirect()->back()->with('success', 'Nominal iuran berhasil disimpan!');
    }
}