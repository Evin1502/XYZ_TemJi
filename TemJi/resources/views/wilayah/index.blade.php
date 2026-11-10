@extends('layouts.app')
@section('title', 'Data Wilayah')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Data Wilayah</h4>
    <a href="{{ route('admin.wilayah.create') }}" class="btn btn-primary">+ Tambah Wilayah</a>
</div>
<table class="table table-bordered bg-white">
    <thead>
        <tr><th>Nama</th><th>Jumlah KK</th><th>Status</th><th>Aksi</th></tr>
    </thead>
    <tbody>
    @forelse ($wilayahs as $w)
        <tr>
            <td>{{ $w->nama }}</td>
            <td>{{ number_format($w->jumlah_kk) }}</td>
            <td>
                <span class="badge {{ $w->is_aktif ? 'bg-success' : 'bg-secondary' }}">
                    {{ $w->is_aktif ? 'Aktif' : 'Nonaktif' }}
                </span>
            </td>
            <td><a href="{{ route('admin.wilayah.edit', $w) }}" class="btn btn-sm btn-warning">Ubah</a></td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center">Belum ada wilayah.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
