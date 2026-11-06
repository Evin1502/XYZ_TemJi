@extends('layouts.admin')

@section('title', 'Verifikasi Identitas Warga')
@section('breadcrumb_parent', 'Operasional')
@section('breadcrumb_current', 'Verifikasi Identitas Warga')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTION -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Verifikasi identitas warga</h2>
            <p class="text-xs text-slate-500 mt-1">
                Validasi kesesuaian KTP/KK dan domisili wilayah Sukapura & Sukabirus sebelum mengaktifkan akses layanan warga.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('warga.registrasi.status') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/90 px-3.5 py-2.5 rounded-xl font-medium text-xs shadow-2xs transition">
                <span>Portal Status Warga</span>
            </a>
            <a href="{{ route('warga.registrasi.form') }}" class="inline-flex items-center gap-2 bg-[#14532D] hover:bg-[#0f3d21] text-white px-4 py-2.5 rounded-xl font-medium text-xs shadow-xs transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Form registrasi warga</span>
            </a>
        </div>
    </div>

    <!-- FILTER TABS & PENCARIAN -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 border-b border-slate-200/60 pb-3">
        <!-- TABS STATUS -->
        <div class="flex flex-wrap items-center gap-2">
            @php
                $tabs = [
                    'semua' => ['label' => 'Semua', 'count' => $totalCount],
                    'menunggu' => ['label' => 'Menunggu', 'count' => $menungguCount],
                    'terverifikasi' => ['label' => 'Terverifikasi', 'count' => $terverifikasiCount],
                    'ditolak' => ['label' => 'Ditolak', 'count' => $ditolakCount],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                @php
                    $isActiveTab = $statusFilter === $key;
                    $tabParams = array_filter([
                        'status' => $key === 'semua' ? null : $key,
                        'wilayah_id' => $wilayahFilter,
                        'q' => $search !== '' ? $search : null,
                    ]);
                @endphp
                <a href="{{ route('admin.verifikasi-warga.index', $tabParams) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs transition {{ $isActiveTab ? 'bg-white font-semibold text-slate-900 shadow-xs border border-slate-200/80' : 'font-medium text-slate-500 hover:text-slate-800' }}">
                    <span>{{ $tab['label'] }}</span>
                    <span class="text-[11px] {{ $isActiveTab ? 'text-[#14532D] font-bold' : 'text-slate-400' }}">{{ $tab['count'] }}</span>
                </a>
            @endforeach
        </div>

        <!-- SEARCH & FILTER WILAYAH -->
        <form method="GET" action="{{ route('admin.verifikasi-warga.index') }}" class="flex flex-wrap items-center gap-2">
            @if($statusFilter !== 'semua')
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <select name="wilayah_id" onchange="this.form.submit()" class="bg-white border border-slate-200/90 rounded-xl px-3 py-1.5 text-xs text-slate-700 focus:ring-2 focus:ring-[#14532D] focus:outline-none">
                <option value="">Semua Wilayah</option>
                @foreach($wilayahList as $wilayah)
                    <option value="{{ $wilayah->id }}" {{ (string) $wilayahFilter === (string) $wilayah->id ? 'selected' : '' }}>
                        {{ $wilayah->nama }} ({{ $wilayah->jumlah_kk }} KK)
                    </option>
                @endforeach
            </select>

            <div class="relative">
                <input
                    type="text"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari nama, NIK, KK, email..."
                    class="bg-white border border-slate-200/90 rounded-xl pl-3 pr-8 py-1.5 text-xs text-slate-800 w-52 focus:ring-2 focus:ring-[#14532D] focus:outline-none"
                >
                <button type="submit" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 cursor-pointer" title="Cari">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </div>

            @if($search !== '' || $wilayahFilter || $statusFilter !== 'semua')
                <a href="{{ route('admin.verifikasi-warga.index') }}" class="text-[11px] text-slate-500 hover:text-slate-800 underline px-1">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- KOLOM KIRI: TABEL ANTREAN PENDAFTAR WARGA (7 COLUMNS) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">{{ $wargaList->count() }} pengajuan akun warga</h3>
                    <p class="text-xs text-slate-400 font-normal">
                        {{ $menungguCount }} menunggu · {{ $terverifikasiCount }} terverifikasi · {{ $ditolakCount }} ditolak
                    </p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[11px] font-semibold text-slate-400 border-b border-slate-100">
                            <th class="pb-3 font-medium">Warga</th>
                            <th class="pb-3 font-medium">NIK & No. KK</th>
                            <th class="pb-3 font-medium">Wilayah</th>
                            <th class="pb-3 font-medium text-center">Status</th>
                            <th class="pb-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($wargaList as $item)
                            @php
                                $isSelected = $selectedWarga && $selectedWarga->id === $item->id;
                                $initials = collect(explode(' ', trim($item->nama_lengkap)))
                                    ->filter()
                                    ->take(2)
                                    ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                                    ->implode('');
                                $rowLinkParams = array_filter([
                                    'status' => $statusFilter !== 'semua' ? $statusFilter : null,
                                    'wilayah_id' => $wilayahFilter,
                                    'q' => $search !== '' ? $search : null,
                                    'selected' => $item->id,
                                ]);
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition {{ $isSelected ? 'bg-[#EBF7EE]/35' : '' }}">
                                <!-- NAMA & EMAIL -->
                                <td class="py-3.5 pr-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full {{ $item->isPending() ? 'bg-amber-50 text-amber-700' : ($item->isVerified() ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700') }} font-bold text-[11px] flex items-center justify-center shrink-0">
                                            {{ $initials ?: 'WG' }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-900 truncate">{{ $item->nama_lengkap }}</div>
                                            <div class="text-[11px] text-slate-400 truncate">{{ $item->user?->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- NIK & KK -->
                                <td class="py-3.5 px-3">
                                    <div class="font-mono text-[11px] font-semibold text-slate-800">{{ $item->nik }}</div>
                                    <div class="font-mono text-[10px] text-slate-400">KK: {{ $item->no_kk }}</div>
                                </td>

                                <!-- WILAYAH -->
                                <td class="py-3.5 px-3">
                                    @if($item->wilayah)
                                        <span class="inline-block {{ $item->wilayah->nama === 'Sukapura' ? 'bg-[#E0F2FE] text-[#0369A1]' : 'bg-[#EBF7EE] text-[#14532D]' }} text-[11px] font-medium px-2.5 py-0.5 rounded-full">
                                            {{ $item->wilayah->nama }}
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">-</span>
                                    @endif
                                </td>

                                <!-- STATUS -->
                                <td class="py-3.5 px-3 text-center">
                                    @if($item->isVerified())
                                        <span class="inline-flex items-center gap-1.5 text-emerald-700 font-semibold text-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            Terverifikasi
                                        </span>
                                    @elseif($item->isRejected())
                                        <span class="inline-flex items-center gap-1.5 text-rose-700 font-semibold text-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            Ditolak
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-amber-700 font-semibold text-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Menunggu
                                        </span>
                                    @endif
                                </td>

                                <!-- AKSI -->
                                <td class="py-3.5 pl-3 text-right">
                                    <a href="{{ route('admin.verifikasi-warga.index', $rowLinkParams) }}"
                                       class="inline-block px-3 py-1.5 bg-white border border-slate-300 hover:border-slate-400 text-slate-700 hover:text-slate-900 rounded-lg text-xs font-medium transition shadow-2xs {{ $isSelected ? 'ring-2 ring-[#14532D] text-[#14532D] font-semibold' : '' }}">
                                        Tinjau
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400 text-xs">
                                    Tidak ada pengajuan warga yang sesuai dengan filter pencarian saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KOLOM KANAN: PANEL TINJAUAN DOKUMEN & KEPUTUSAN VERIFIKASI (5 COLUMNS) -->
        <div class="lg:col-span-5 space-y-6">
            @if($selectedWarga)
                @php
                    $selectedBadge = $selectedWarga->badge_status;
                    $fileExists = $selectedWarga->foto_identitas
                        && \Illuminate\Support\Facades\Storage::disk('public')->exists($selectedWarga->foto_identitas);
                    $fotoUrl = $fileExists
                        ? \Illuminate\Support\Facades\Storage::url($selectedWarga->foto_identitas)
                        : null;
                @endphp

                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                    <!-- HEADER PANEL -->
                    <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Detail identitas warga</h3>
                            <p class="text-xs text-slate-500 font-normal mt-0.5">{{ $selectedWarga->nama_lengkap }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[11px] font-semibold {{ $selectedBadge['bg'] }} {{ $selectedBadge['text'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $selectedBadge['dot'] }}"></span>
                            {{ $selectedBadge['label'] }}
                        </span>
                    </div>

                    <!-- PRATINJAU DOKUMEN KTP / KK -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-700">Bukti Dokumen Identitas (KTP / KK)</span>
                            @if($fotoUrl)
                                <button type="button" onclick="openDocModal()" class="text-[11px] font-semibold text-[#14532D] hover:underline cursor-pointer">
                                    Perbesar gambar
                                </button>
                            @endif
                        </div>

                        @if($fotoUrl)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-2 overflow-hidden">
                                <img
                                    src="{{ $fotoUrl }}"
                                    alt="Bukti Identitas {{ $selectedWarga->nama_lengkap }}"
                                    onclick="openDocModal()"
                                    class="w-full max-h-52 object-contain rounded-lg cursor-zoom-in mx-auto"
                                >
                            </div>
                        @else
                            <!-- PRATINJAU KARTU IDENTITAS DIGITAL UNTUK DATA SEEDER -->
                            <div class="rounded-xl border border-sky-200 bg-gradient-to-br from-sky-50 to-cyan-50/70 p-4 space-y-2.5 text-xs shadow-2xs">
                                <div class="flex items-center justify-between border-b border-sky-200/80 pb-2">
                                    <div>
                                        <div class="text-[10px] font-bold uppercase tracking-wider text-sky-900">Provinsi Jawa Barat · Kab. Bandung</div>
                                        <div class="text-[10px] text-sky-700">Pratinjau Dokumen KTP/KK Terlampir</div>
                                    </div>
                                    <span class="bg-white/90 text-sky-800 border border-sky-200 text-[10px] font-semibold px-2 py-0.5 rounded-md">
                                        Arsip Digital
                                    </span>
                                </div>
                                <div class="space-y-1 text-[11px]">
                                    <div class="flex justify-between gap-2">
                                        <span class="text-sky-700">NIK</span>
                                        <span class="font-mono font-bold text-slate-900">{{ $selectedWarga->nik }}</span>
                                    </div>
                                    <div class="flex justify-between gap-2">
                                        <span class="text-sky-700">No. KK</span>
                                        <span class="font-mono font-semibold text-slate-800">{{ $selectedWarga->no_kk }}</span>
                                    </div>
                                    <div class="flex justify-between gap-2">
                                        <span class="text-sky-700">Nama</span>
                                        <span class="font-semibold text-slate-900 text-right">{{ $selectedWarga->nama_lengkap }}</span>
                                    </div>
                                    <div class="flex justify-between gap-2">
                                        <span class="text-sky-700">Desa / Wilayah</span>
                                        <span class="font-semibold text-slate-900">{{ $selectedWarga->wilayah?->nama ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="pt-1 text-[10px] text-slate-500 truncate">
                                    Berkas: <span class="font-mono">{{ $selectedWarga->foto_identitas }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- RINGKASAN DATA WARGA -->
                    <div class="grid grid-cols-2 gap-3 text-xs bg-slate-50/90 rounded-xl p-3.5 border border-slate-100">
                        <div>
                            <div class="text-[11px] text-slate-400">NIK</div>
                            <div class="font-mono font-semibold text-slate-900 mt-0.5">{{ $selectedWarga->nik }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-400">Nomor KK</div>
                            <div class="font-mono font-semibold text-slate-900 mt-0.5">{{ $selectedWarga->no_kk }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-400">Email Akun</div>
                            <div class="font-medium text-slate-800 mt-0.5 truncate">{{ $selectedWarga->user?->email ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-400">Nomor HP / WA</div>
                            <div class="font-medium text-slate-800 mt-0.5">{{ $selectedWarga->no_hp }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-400">Wilayah Domisili</div>
                            <div class="font-semibold text-slate-900 mt-0.5">{{ $selectedWarga->wilayah?->nama ?? '-' }}</div>
                        </div>
                        <div>
                            <div class="text-[11px] text-slate-400">Tanggal Daftar</div>
                            <div class="font-medium text-slate-800 mt-0.5">
                                {{ $selectedWarga->created_at ? $selectedWarga->created_at->translatedFormat('d M Y, H:i') : '-' }}
                            </div>
                        </div>
                        <div class="col-span-2 pt-1 border-t border-slate-200/60">
                            <div class="text-[11px] text-slate-400">Alamat Lengkap</div>
                            <div class="font-medium text-slate-800 mt-0.5 leading-relaxed">{{ $selectedWarga->alamat }}</div>
                        </div>

                        @if($selectedWarga->diverifikasi_pada || $selectedWarga->catatan_verifikasi)
                            <div class="col-span-2 pt-2 border-t border-slate-200/60 space-y-1">
                                <div class="text-[11px] text-slate-400">
                                    Riwayat Keputusan
                                    @if($selectedWarga->verifikator)
                                        oleh <span class="font-semibold text-slate-600">{{ $selectedWarga->verifikator->name }}</span>
                                    @endif
                                    @if($selectedWarga->diverifikasi_pada)
                                        ({{ $selectedWarga->diverifikasi_pada->translatedFormat('d M Y, H:i') }})
                                    @endif
                                </div>
                                @if($selectedWarga->catatan_verifikasi)
                                    <div class="text-slate-700 italic">"{{ $selectedWarga->catatan_verifikasi }}"</div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- TINDAKAN VERIFIKASI: SETUJUI -->
                    <div class="pt-2 border-t border-slate-100 space-y-3">
                        <form method="POST" action="{{ route('admin.verifikasi-warga.approve', $selectedWarga->id) }}" class="space-y-3">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="approve_catatan" class="block text-xs font-medium text-slate-700 mb-1">
                                    Catatan Persetujuan (Opsional)
                                </label>
                                <input
                                    type="text"
                                    name="catatan_verifikasi"
                                    id="approve_catatan"
                                    placeholder="Contoh: KTP & alamat sesuai domisili RT/RW wilayah."
                                    class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none"
                                >
                            </div>

                            <button type="submit" class="w-full bg-[#14532D] hover:bg-[#0f3d21] text-white py-2.5 rounded-xl font-semibold text-xs shadow-xs transition cursor-pointer flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ $selectedWarga->isVerified() ? 'Perbarui Persetujuan Verifikasi' : 'Setujui & Verifikasi Identitas Warga' }}</span>
                            </button>
                        </form>

                        <!-- TINDAKAN VERIFIKASI: TOLAK DENGAN ALASAN -->
                        <div class="pt-3 border-t border-slate-100">
                            <form method="POST" action="{{ route('admin.verifikasi-warga.reject', $selectedWarga->id) }}" class="space-y-2.5">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="reject_catatan" class="block text-xs font-medium text-rose-700 mb-1">
                                        Alasan Penolakan / Permintaan Perbaikan <span class="text-rose-600">*</span>
                                    </label>
                                    <textarea
                                        name="catatan_verifikasi"
                                        id="reject_catatan"
                                        rows="2"
                                        required
                                        minlength="5"
                                        placeholder="Wajib diisi jika menolak (contoh: Foto KTP buram, mohon unggah ulang foto yang jelas)..."
                                        class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-rose-500 focus:border-transparent focus:outline-none"
                                    ></textarea>
                                </div>

                                <button type="submit" class="w-full bg-white hover:bg-rose-50 text-rose-700 border border-rose-300 py-2 rounded-xl font-semibold text-xs transition cursor-pointer">
                                    Tolak Pengajuan & Minta Unggah Ulang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                @if($fotoUrl)
                    <!-- MODAL ZOOM DOKUMEN IDENTITAS -->
                    <div id="docModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden" onclick="closeDocModal()">
                        <div class="bg-white rounded-2xl max-w-2xl w-full p-5 shadow-xl space-y-3" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">Dokumen Identitas — {{ $selectedWarga->nama_lengkap }}</h4>
                                    <p class="text-[11px] text-slate-500 font-mono">NIK: {{ $selectedWarga->nik }} · KK: {{ $selectedWarga->no_kk }}</p>
                                </div>
                                <button type="button" onclick="closeDocModal()" class="text-slate-400 hover:text-slate-800 text-xl font-bold leading-none cursor-pointer">&times;</button>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-3 flex items-center justify-center">
                                <img src="{{ $fotoUrl }}" alt="Zoom Bukti Identitas" class="max-h-[70vh] w-auto object-contain rounded-lg">
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8 text-center space-y-2">
                    <p class="text-sm font-semibold text-slate-700">Belum ada warga dipilih</p>
                    <p class="text-xs text-slate-400">Pilih salah satu pengajuan warga pada tabel di sebelah kiri untuk meninjau dokumen KTP/KK.</p>
                </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>
    function openDocModal() {
        const modal = document.getElementById('docModal');
        if (modal) modal.classList.remove('hidden');
    }

    function closeDocModal() {
        const modal = document.getElementById('docModal');
        if (modal) modal.classList.add('hidden');
    }
</script>
@endpush
@endsection
