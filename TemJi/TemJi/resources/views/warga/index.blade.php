@extends('layouts.app')
@section('title', 'KK Warga')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">KK Warga</h4>
    <a href="{{ route('warga.create') }}" class="btn btn-primary">+ Tambah KK</a>
</div>

@if ($jumlahBelumBerwilayah > 0)
    <div class="alert alert-warning d-flex justify-content-between align-items-center">
        <span><strong>{{ $jumlahBelumBerwilayah }}</strong> KK belum memiliki wilayah, statistik belum lengkap.</span>
        <a href="{{ route('warga.index', ['belum_berwilayah' => 1]) }}" class="btn btn-sm btn-dark">Tampilkan yang belum berwilayah</a>
    </div>
@endif

@if (request()->boolean('belum_berwilayah'))
    <a href="{{ route('warga.index') }}" class="btn btn-sm btn-outline-secondary mb-3">Tampilkan semua</a>
@endif

<form action="{{ route('warga.bulk-assign') }}" method="POST">
    @csrf
    @if ($errors->has('warga_ids') || $errors->has('wilayah_id'))
        <div class="alert alert-danger">{{ $errors->first('warga_ids') ?: $errors->first('wilayah_id') }}</div>
    @endif
    <div class="input-group mb-3" style="max-width:520px">
        <select name="wilayah_id" class="form-select">
            <option value="">-- Set wilayah untuk KK terpilih --</option>
            @foreach ($wilayahs as $w)
                <option value="{{ $w->id }}">{{ $w->nama }}</option>
            @endforeach
        </select>
        <button class="btn btn-success" type="submit">Terapkan</button>
    </div>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th style="width:40px"><input type="checkbox" onclick="document.querySelectorAll('.cek').forEach(c => c.checked = this.checked)"></th>
                <th>No. KK</th><th>Kepala Keluarga</th><th>Wilayah</th><th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($wargas as $g)
            <tr>
                <td><input type="checkbox" class="cek" name="warga_ids[]" value="{{ $g->id }}"></td>
                <td>{{ $g->no_kk }}</td>
                <td>{{ $g->nama_kepala_keluarga }}</td>
                <td>
                    @if ($g->wilayah)
                        {{ $g->wilayah->nama }}
                    @else
                        <span class="badge bg-danger">Belum diisi</span>
                    @endif
                </td>
                <td><a href="{{ route('warga.edit', $g) }}" class="btn btn-sm btn-warning">Ubah</a></td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-center">Belum ada data KK warga.</td></tr>
        @endforelse
        </tbody>
    </table>
</form>
{{ $wargas->links('pagination::bootstrap-5') }}
@endsection
