@extends('layouts.app')
@section('title', 'Ubah KK Warga')
@section('content')
<h4 class="mb-3">Ubah KK Warga</h4>
<form action="{{ route('warga.update', $warga) }}" method="POST">
    @csrf
    @method('PUT')
    @include('warga._form', ['warga' => $warga])
</form>
@endsection
