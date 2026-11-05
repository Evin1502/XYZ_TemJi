@extends('layouts.admin')

@section('title', 'Jadwal Penjemputan')
@section('breadcrumb_induk', 'Operasional')
@section('breadcrumb', 'Jadwal')

@use('App\Models\JadwalPenjemputan')

@php
    // Query string halaman dengan filter yang sedang aktif, bisa ditimpa per tautan
    $urlJadwal = fn (array $ubah = []) => route('admin.jadwal.index', array_filter(array_merge([
        'minggu' => $senin->toDateString(),
        'wilayah' => $wilayahId,
    ], $ubah)));

    $tabWilayah = fn (bool $aktif) => $aktif
        ? 'px-4 py-1.5 rounded-lg bg-white text-slate-900 font-semibold shadow-xs'
        : 'px-4 py-1.5 rounded-lg text-slate-500 hover:text-slate-800 transition';

    $input = 'w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#14532D]/20 focus:border-[#14532D]';
    $border = fn (string $field) => $errors->has($field) ? 'border-rose-400' : 'border-slate-200';
    $label = 'block text-xs font-semibold text-slate-700 mb-1.5';

    $jenisDipilih = old('jenis', JadwalPenjemputan::JENIS_DAUR_ULANG);
@endphp

@section('content')
    <!-- HEADER HALAMAN & FILTER WILAYAH -->
    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">Jadwal penjemputan</h2>
            <p class="mt-1 text-sm text-slate-500">Jadwal rutin sampah biasa dan jadwal daur ulang dengan kuota, per wilayah dan petugas.</p>
        </div>

        <nav class="inline-flex items-center gap-1 rounded-xl bg-slate-100 p-1 text-sm" aria-label="Filter wilayah">
            <a href="{{ $urlJadwal(['wilayah' => null]) }}" class="{{ $tabWilayah($wilayahId === null) }}">Semua</a>
            @foreach($wilayahList as $wilayah)
                <a href="{{ $urlJadwal(['wilayah' => $wilayah->id]) }}" class="{{ $tabWilayah($wilayahId === $wilayah->id) }}">{{ $wilayah->nama }}</a>
            @endforeach
        </nav>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_20rem] gap-6 items-start">
        <!-- KALENDER MINGGUAN -->
        <section class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <a href="{{ $urlJadwal(['minggu' => $mingguSebelumnya]) }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition" aria-label="Minggu sebelumnya">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <h3 class="text-base font-bold text-slate-900">{{ $labelMinggu }}</h3>
                    <a href="{{ $urlJadwal(['minggu' => $mingguBerikutnya]) }}" class="w-9 h-9 rounded-xl border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50 transition" aria-label="Minggu berikutnya">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
                <div class="flex items-center gap-2 text-xs font-medium">
                    <span class="px-3 py-1 rounded-full bg-[#EBF7EE] text-[#14532D]">Sampah biasa</span>
                    <span class="px-3 py-1 rounded-full bg-[#E8F0FB] text-[#1E4E8C]">Daur ulang</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <div class="grid grid-cols-6 min-w-[720px] divide-x divide-slate-100">
                    @foreach($hariMinggu as $kolom)
                        <div class="min-h-[30rem] p-2.5 space-y-2" data-kolom-hari="{{ $kolom['hari'] }}">
                            <div class="flex items-center justify-between px-1 pb-1 text-xs text-slate-500">
                                <span class="font-medium">{{ $kolom['label'] }}</span>
                                <span>{{ $kolom['tanggal']->day }}</span>
                            </div>

                            @foreach($jadwalPerHari->get($kolom['hari'], collect()) as $jadwal)
                                @if($jadwal->isDaurUlang())
                                    <div class="rounded-xl bg-[#E8F0FB] px-2.5 py-2 text-[#1E4E8C]">
                                        <div class="text-sm font-bold whitespace-nowrap">{{ $jadwal->rentang_waktu }}</div>
                                        <div class="mt-0.5 text-xs leading-snug">Daur ulang · {{ $jadwal->wilayah->nama }}</div>
                                        <div class="text-xs leading-snug">
                                            Kuota {{ $jadwal->jumlahTerisi($kolom['tanggal']) }}/{{ $jadwal->kuota }} ·
                                            {{ $jadwal->isPenuh($kolom['tanggal']) ? 'Penuh' : $jadwal->petugas->name }}
                                        </div>
                                    </div>
                                @else
                                    <div class="rounded-xl bg-[#EBF7EE] px-2.5 py-2 text-[#14532D]">
                                        <div class="text-sm font-bold whitespace-nowrap">{{ $jadwal->rentang_waktu }}</div>
                                        <div class="mt-0.5 text-xs leading-snug">Sampah biasa · {{ $jadwal->wilayah->nama }}</div>
                                        <div class="text-xs leading-snug">{{ $jadwal->petugas->name }}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- FORM TAMBAH JADWAL -->
        <section class="bg-white rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="px-6 py-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Tambah jadwal</h3>
                <p class="text-xs text-slate-400 mt-0.5">Berulang tiap minggu</p>
            </div>

            <form id="form-jadwal" method="POST" action="{{ route('admin.jadwal.store') }}" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="minggu" value="{{ $senin->toDateString() }}">
                <input type="hidden" name="filter_wilayah" value="{{ $wilayahId }}">

                <div>
                    <span class="{{ $label }}">Jenis jadwal</span>
                    <div class="grid grid-cols-2 gap-1 rounded-xl bg-slate-100 p-1">
                        @foreach(JadwalPenjemputan::JENIS as $nilai => $nama)
                            <label class="cursor-pointer">
                                <input type="radio" name="jenis" value="{{ $nilai }}" class="peer sr-only" @checked($jenisDipilih === $nilai)>
                                <span class="block rounded-lg py-2 text-center text-sm text-slate-500 transition peer-checked:bg-white peer-checked:font-semibold peer-checked:text-slate-900 peer-checked:shadow-xs peer-focus-visible:ring-2 peer-focus-visible:ring-[#14532D]/30">{{ $nama }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="wilayah_id" class="{{ $label }}">Wilayah</label>
                    <select id="wilayah_id" name="wilayah_id" class="{{ $input }} {{ $border('wilayah_id') }}">
                        <option value="">Pilih wilayah</option>
                        @foreach($wilayahList as $wilayah)
                            <option value="{{ $wilayah->id }}" @selected((int) old('wilayah_id', $wilayahId) === $wilayah->id)>{{ $wilayah->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <span class="{{ $label }}">Hari</span>
                    <div class="grid grid-cols-6 gap-1.5">
                        @foreach(JadwalPenjemputan::HARI as $nilai => $nama)
                            <label class="cursor-pointer">
                                <input type="radio" name="hari" value="{{ $nilai }}" class="peer sr-only" @checked((int) old('hari') === $nilai)>
                                <span class="block rounded-full bg-slate-100 py-1.5 text-center text-xs text-slate-600 transition hover:bg-slate-200 peer-checked:bg-[#EBF7EE] peer-checked:font-semibold peer-checked:text-[#14532D] peer-focus-visible:ring-2 peer-focus-visible:ring-[#14532D]/30 {{ $errors->has('hari') ? 'ring-1 ring-rose-300' : '' }}">{{ $nama }}</span>
                            </label>
                        @endforeach
                    </div>
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

                <div class="grid grid-cols-2 gap-3">
                    <div id="grup-kuota">
                        <label for="kuota" class="{{ $label }}">Kuota</label>
                        <div class="flex rounded-xl border bg-white overflow-hidden focus-within:ring-2 focus-within:ring-[#14532D]/20 {{ $border('kuota') }}">
                            <input type="number" id="kuota" name="kuota" min="1" value="{{ old('kuota', 20) }}" class="w-full min-w-0 px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none disabled:bg-slate-50">
                            <span class="px-3 flex items-center border-l border-slate-200 bg-slate-50 text-xs text-slate-500">rumah</span>
                        </div>
                    </div>
                    <div>
                        <label for="petugas_id" class="{{ $label }}">Petugas</label>
                        <select id="petugas_id" name="petugas_id" class="{{ $input }} {{ $border('petugas_id') }}">
                            <option value="">Pilih</option>
                            @foreach($petugasList as $petugas)
                                <option value="{{ $petugas->id }}" @selected((int) old('petugas_id') === $petugas->id)>{{ $petugas->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed">Kuota hanya untuk daur ulang. Warga memilih slot ini saat mengajukan.</p>

                <div class="flex justify-end gap-3 pt-1">
                    <button type="reset" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#14532D] text-sm font-semibold text-white shadow-xs hover:bg-[#166534] transition">Simpan jadwal</button>
                </div>
            </form>
        </section>
    </div>
@endsection

@push('scripts')
<script>
    (() => {
        const form = document.getElementById('form-jadwal');
        const kuota = form.elements.kuota;
        const grupKuota = document.getElementById('grup-kuota');

        const terpilih = (nama) => form.querySelector(`[name="${nama}"]:checked`)?.value;
        const teksOpsi = (select) => select.value ? select.options[select.selectedIndex].text : '–';
        const jam = (nilai) => nilai ? nilai.replace(':', '.') : '--.--';

        const baris = (teks, kelas = 'text-xs leading-snug') => {
            const el = document.createElement('div');
            el.className = kelas;
            el.textContent = teks;
            return el;
        };

        // Pratinjau kartu "Draf" di kolom hari yang dipilih, mengikuti isian form
        function perbarui() {
            const daurUlang = terpilih('jenis') === 'daur_ulang';
            kuota.disabled = !daurUlang;
            grupKuota.classList.toggle('opacity-50', !daurUlang);

            document.querySelectorAll('[data-draf]').forEach((el) => el.remove());

            const hari = terpilih('hari');
            const kolom = hari && document.querySelector(`[data-kolom-hari="${hari}"]`);
            if (!kolom) return;

            const draf = document.createElement('div');
            draf.dataset.draf = '';
            draf.className = 'rounded-xl border-2 border-dashed border-[#14532D]/50 bg-white px-2.5 py-2 text-[#14532D]';
            draf.append(
                baris(`${jam(form.elements.jam_mulai.value)}–${jam(form.elements.jam_selesai.value)}`, 'text-sm font-bold whitespace-nowrap'),
                baris(`Draf · ${daurUlang ? 'Daur ulang' : 'Sampah biasa'} · ${teksOpsi(form.elements.wilayah_id)}`, 'mt-0.5 text-xs leading-snug'),
                baris(daurUlang
                    ? `Kuota ${kuota.value || '–'} · ${teksOpsi(form.elements.petugas_id)}`
                    : teksOpsi(form.elements.petugas_id)),
            );
            kolom.append(draf);
        }

        form.addEventListener('input', perbarui);
        form.addEventListener('change', perbarui);
        form.addEventListener('reset', () => setTimeout(perbarui));
        perbarui();
    })();
</script>
@endpush
