@extends('layouts.admin')

@section('title', 'Penjemputan Tambahan')
@section('breadcrumb_parent', 'Operasional')
@section('breadcrumb_current', 'Penjemputan Tambahan')

@use('App\Models\JadwalPenjemputan')
@use('App\Models\PenjemputanTambahan')

@php
    // Query string halaman dengan filter yang sedang aktif, bisa ditimpa per tautan
    $url = fn (array $ubah = []) => route('admin.penjemputan-tambahan.index', array_filter(array_merge([
        'status' => $status,
        'wilayah' => $wilayahId,
    ], $ubah)));

    $tab = fn (bool $aktif) => $aktif
        ? 'px-3.5 py-1.5 rounded-lg bg-white text-slate-900 font-semibold shadow-xs'
        : 'px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition';

    $input = 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#14532D]/20 focus:border-[#14532D]';
    $border = fn (string $field) => $errors->has($field) ? 'border-rose-400' : 'border-slate-200';
    $label = 'block text-xs font-semibold text-slate-700 mb-1.5';

    $tanggalPanjang = fn ($tanggal) => $tanggal->locale('id')->translatedFormat('l, j F Y');
@endphp

@section('content')
    <!-- HEADER HALAMAN & FILTER WILAYAH -->
    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Penjemputan tambahan</h2>
            <p class="mt-1 text-sm text-slate-500">Setujui atau tolak pengajuan warga, lalu masukkan ke jadwal petugas tanpa menabrak rute rutin.</p>
        </div>

        <nav class="inline-flex items-center gap-1 rounded-xl bg-slate-100 p-1 text-sm" aria-label="Filter wilayah">
            <a href="{{ $url(['wilayah' => null]) }}" class="{{ $tab($wilayahId === null) }}">Semua</a>
            @foreach($wilayahList as $wilayah)
                <a href="{{ $url(['wilayah' => $wilayah->id]) }}" class="{{ $tab($wilayahId === $wilayah->id) }}">{{ $wilayah->nama }}</a>
            @endforeach
        </nav>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_22rem] gap-6 items-start">
        <!-- DAFTAR PENGAJUAN -->
        <section class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 overflow-x-auto">
                <nav class="inline-flex items-center gap-1 rounded-xl bg-slate-100 p-1 text-xs whitespace-nowrap" aria-label="Filter status">
                    @foreach(PenjemputanTambahan::STATUS as $nilai => $nama)
                        <a href="{{ $url(['status' => $nilai]) }}" class="{{ $tab($status === $nilai) }}">{{ $nama }} ({{ $jumlahPerStatus->get($nilai, 0) }})</a>
                    @endforeach
                    <a href="{{ $url(['status' => 'semua']) }}" class="{{ $tab($status === 'semua') }}">Semua ({{ $jumlahPerStatus->sum() }})</a>
                </nav>
            </div>

            @forelse($pengajuanList as $pengajuan)
                <a href="{{ $url(['pilih' => $pengajuan->id]) }}"
                   class="block px-5 py-4 border-b border-slate-100 last:border-b-0 transition {{ $terpilih?->is($pengajuan) ? 'bg-[#EBF7EE]/60' : 'hover:bg-slate-50' }}">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-sm font-bold text-slate-900">{{ $pengajuan->kode }}</span>
                            <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full border {{ $pengajuan->badge_status }}">{{ $pengajuan->status_label }}</span>
                        </div>
                        <span class="text-xs text-slate-500">{{ $tanggalPanjang($pengajuan->tanggal_diminta) }}</span>
                    </div>
                    <div class="mt-1 text-xs text-slate-600">{{ $pengajuan->user->name }} · {{ $pengajuan->wilayah->nama }}</div>
                    <p class="mt-1 text-xs text-slate-500 leading-relaxed">{{ $pengajuan->alasan }}</p>
                </a>
            @empty
                <p class="px-5 py-10 text-center text-xs text-slate-400">Tidak ada pengajuan pada filter ini.</p>
            @endforelse
        </section>

        <!-- PANEL PROSES -->
        <section class="bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            @if(! $terpilih)
                <p class="px-6 py-10 text-center text-xs text-slate-400">Pilih pengajuan untuk diproses.</p>
            @else
                <div class="px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-base font-bold text-slate-900">{{ $terpilih->kode }}</h3>
                        <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full border {{ $terpilih->badge_status }}">{{ $terpilih->status_label }}</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">Diajukan {{ $terpilih->created_at->locale('id')->translatedFormat('j F Y') }}</p>
                </div>

                <dl class="px-6 py-4 space-y-3 text-xs border-b border-slate-100">
                    <div>
                        <dt class="text-slate-400">Warga</dt>
                        <dd class="font-semibold text-slate-800">{{ $terpilih->user->name }} · {{ $terpilih->wilayah->nama }}</dd>
                        @if($terpilih->user->warga)
                            <dd class="text-slate-500">{{ $terpilih->user->warga->alamat }} · {{ $terpilih->user->warga->no_hp }}</dd>
                        @endif
                    </div>
                    <div>
                        <dt class="text-slate-400">Diminta untuk</dt>
                        <dd class="font-semibold text-slate-800">{{ $tanggalPanjang($terpilih->tanggal_diminta) }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Alasan</dt>
                        <dd class="text-slate-700 leading-relaxed">{{ $terpilih->alasan }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-400">Jadwal rutin {{ $terpilih->wilayah->nama }} di hari itu</dt>
                        @forelse($jadwalRutinHariItu as $jadwal)
                            <dd class="text-slate-700">{{ $jadwal->rentang_waktu }} · {{ JadwalPenjemputan::JENIS[$jadwal->jenis] }} · {{ $jadwal->petugas->name }}</dd>
                        @empty
                            <dd class="text-slate-500">Tidak ada jadwal rutin.</dd>
                        @endforelse
                    </div>
                </dl>

                @if($terpilih->isMenunggu())
                    <!-- FORM SETUJUI -->
                    <form method="POST" action="{{ route('admin.penjemputan-tambahan.setujui', $terpilih->id) }}" class="px-6 py-5 space-y-4 border-b border-slate-100">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="petugas_id" class="{{ $label }}">Petugas</label>
                            <select id="petugas_id" name="petugas_id" class="{{ $input }} {{ $border('petugas_id') }}">
                                <option value="">Pilih petugas</option>
                                @foreach($petugasList as $petugas)
                                    <option value="{{ $petugas->id }}" @selected((int) old('petugas_id') === $petugas->id)>
                                        {{ $petugas->name }}{{ $petugas->wilayah ? ' · ' . $petugas->wilayah->nama : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="tanggal_jemput" class="{{ $label }}">Tanggal jemput</label>
                            <input type="date" id="tanggal_jemput" name="tanggal_jemput"
                                   value="{{ old('tanggal_jemput', $terpilih->tanggal_diminta->toDateString()) }}"
                                   class="{{ $input }} {{ $border('tanggal_jemput') }}">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="jam_mulai" class="{{ $label }}">Mulai</label>
                                <input type="time" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" class="{{ $input }} {{ $border('jam_mulai') }}">
                            </div>
                            <div>
                                <label for="jam_selesai" class="{{ $label }}">Selesai</label>
                                <input type="time" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" class="{{ $input }} {{ $border('jam_selesai') }}">
                            </div>
                        </div>

                        <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-[#14532D] text-sm font-semibold text-white shadow-xs hover:bg-[#166534] transition cursor-pointer">Setujui & jadwalkan</button>
                    </form>

                    <!-- FORM TOLAK -->
                    <form method="POST" action="{{ route('admin.penjemputan-tambahan.tolak', $terpilih->id) }}" class="px-6 py-5 space-y-3">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="alasan_penolakan" class="{{ $label }}">Alasan penolakan</label>
                            <textarea id="alasan_penolakan" name="alasan_penolakan" rows="3" maxlength="500"
                                      placeholder="Alasan ini akan dilihat warga."
                                      class="{{ $input }} {{ $border('alasan_penolakan') }}">{{ old('alasan_penolakan') }}</textarea>
                        </div>

                        <button type="submit" class="w-full px-5 py-2.5 rounded-xl border border-rose-200 text-sm font-semibold text-rose-700 hover:bg-rose-50 transition cursor-pointer">Tolak pengajuan</button>
                    </form>
                @else
                    <dl class="px-6 py-4 space-y-3 text-xs">
                        @if($terpilih->isDisetujui())
                            <div>
                                <dt class="text-slate-400">Dijadwalkan</dt>
                                <dd class="font-semibold text-slate-800">{{ $tanggalPanjang($terpilih->tanggal_jemput) }} · {{ $terpilih->rentang_waktu }}</dd>
                                <dd class="text-slate-700">{{ $terpilih->petugas?->name ?? 'Petugas tidak tersedia' }}</dd>
                            </div>
                        @elseif($terpilih->alasan_penolakan)
                            <div>
                                <dt class="text-slate-400">Alasan penolakan</dt>
                                <dd class="text-slate-700 leading-relaxed">{{ $terpilih->alasan_penolakan }}</dd>
                            </div>
                        @endif
                        @if($terpilih->diproses_pada)
                            <div class="text-slate-400">Diproses {{ $terpilih->diproses_pada->locale('id')->translatedFormat('j F Y') }}</div>
                        @endif
                    </dl>
                @endif
            @endif
        </section>
    </div>
@endsection
