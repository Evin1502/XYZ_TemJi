<?php

namespace App\Http\Controllers;

use App\Http\Requests\WilayahRequest;
use App\Models\Wilayah;

class WilayahController extends Controller
{
    public function index()
    {
        $wilayahs = Wilayah::orderBy('nama')->get();
        return view('wilayah.index', compact('wilayahs'));
    }

    public function create()
    {
        return view('wilayah.create');
    }

    public function store(WilayahRequest $request)
    {
        Wilayah::create($request->validated());
        return redirect()->route('wilayah.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function edit(Wilayah $wilayah)
    {
        return view('wilayah.edit', compact('wilayah'));
    }

    public function update(WilayahRequest $request, Wilayah $wilayah)
    {
        $wilayah->update($request->validated());
        return redirect()->route('wilayah.index')->with('success', 'Wilayah berhasil diperbarui.');
    }
}