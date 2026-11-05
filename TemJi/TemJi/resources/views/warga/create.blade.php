@extends('layouts.app')
@section('title', 'Tambah KK Warga')
@section('content')
<h4 class="mb-3">Tambah KK Warga</h4>
<form action="{{ route('warga.store') }}" method="POST">
    @csrf
    @include('warga._form', ['warga' => null])
</form>
@endsection
