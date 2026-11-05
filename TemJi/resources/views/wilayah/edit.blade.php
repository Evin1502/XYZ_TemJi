@extends('layouts.app')
@section('title', 'Ubah Wilayah')
@section('content')
<h4 class="mb-3">Ubah Wilayah</h4>
<form action="{{ route('wilayah.update', $wilayah) }}" method="POST">
    @csrf
    @method('PUT')
    @include('wilayah._form', ['wilayah' => $wilayah])
</form>
@endsection