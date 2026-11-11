<?php

namespace App\Http\Controllers;

use App\Http\Requests\WargaRequest;
use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $query = Warga::with('wilayah')->orderBy('nama_lengkap');

        if ($request->boolean('belum_berwilayah')) {
            $query->belumBerwilayah();   // scope sudah ada di model Warga milik Farhan
        }

        return view('warga.index', [
            'wargas' => $query->paginate(20)->withQueryString(),
            'jumlahBelumBerwilayah' => Warga::belumBerwilayah()->count(),
            'wilayahs' => Wilayah::aktif()->orderBy('nama')->get(),
        ]);
    }

    public function edit(Warga $warga)
    {
        return view('warga.edit', [
            'warga' => $warga,
            'wilayahs' => Wilayah::aktif()->orderBy('nama')->get(),
        ]);
    }

    public function update(WargaRequest $request, Warga $warga)
    {
        $warga->update($request->validated());   // hanya wilayah_id

        return redirect()->route('admin.warga.index')->with('success', 'Wilayah warga berhasil diperbarui.');
    }

    public function bulkAssign(Request $request)
    {
        $data = $request->validate([
            'warga_ids'   => ['required', 'array', 'min:1'],
            'warga_ids.*' => ['integer', 'exists:warga,id'],
            'wilayah_id'  => ['required', Rule::exists('wilayah', 'id')->where('is_aktif', true)],
        ], [
            'warga_ids.required'  => 'Pilih minimal satu warga.',
            'wilayah_id.required' => 'Pilih wilayah tujuan.',
        ]);

        $jumlah = Warga::whereIn('id', $data['warga_ids'])->update(['wilayah_id' => $data['wilayah_id']]);

        return back()->with('success', "Wilayah berhasil dilengkapi untuk {$jumlah} warga.");
    }
}
