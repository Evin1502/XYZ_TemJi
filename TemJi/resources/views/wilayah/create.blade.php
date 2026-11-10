@extends('layouts.app')
@section('title', 'Tambah Wilayah')
@section('content')
<h4 class="mb-3">Tambah Wilayah</h4>
<form action="{{ route('admin.wilayah.store') }}" method="POST">
    @csrf
    @include('wilayah._form', ['wilayah' => null])
</form>
@endsection
