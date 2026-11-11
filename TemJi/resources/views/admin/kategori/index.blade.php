@extends('layouts.admin')

@section('title', 'Kategori Daur Ulang')

@section('content')
<div class="space-y-6">

    <!-- HEADER & ACTION -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Kategori daur ulang</h2>
            <p class="text-xs text-slate-500 mt-1">
                Tambah kategori tanpa mengubah kode. Tarif baru hanya berlaku untuk setoran setelah tanggal berlaku.
            </p>
        </div>
        <div>
            <button type="button" onclick="openAddModal()" class="inline-flex items-center gap-2 bg-[#14532D] hover:bg-[#0f3d21] text-white px-4 py-2.5 rounded-xl font-medium text-xs shadow-xs transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah kategori</span>
            </button>
        </div>
    </div>

    <!-- TABS -->
    <div class="flex items-center gap-2 border-b border-slate-200/60 pb-3">
        <a href="{{ route('admin.kategori.index') }}" class="bg-white px-4 py-1.5 rounded-full text-xs font-semibold text-slate-900 shadow-xs border border-slate-200/80">
            Kategori
        </a>
        <a href="{{ route('admin.aturan-penukaran.index') }}" class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-500 hover:text-slate-800 transition">
            Aturan penukaran
        </a>
    </div>

    <!-- TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- KOLOM KIRI: TABEL MASTER KATEGORI (7 COLUMNS) -->
        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <!-- HEADER TABEL -->
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">{{ $totalCount }} kategori</h3>
                    <p class="text-xs text-slate-400 font-normal">{{ $activeCount }} aktif · {{ $inactiveCount }} nonaktif</p>
                </div>
            </div>

            <!-- TABEL KATEGORI -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[11px] font-semibold text-slate-400 border-b border-slate-100">
                            <th class="pb-3 font-medium">Kategori</th>
                            <th class="pb-3 font-medium">Tipe</th>
                            <th class="pb-3 font-medium text-center">Poin per kg</th>
                            <th class="pb-3 font-medium text-center">Status</th>
                            <th class="pb-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($kategoriList as $item)
                        @php
                            $isSelected = $selectedKategori && $selectedKategori->id === $item->id;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition {{ $isSelected ? 'bg-slate-50/60' : '' }}">
                            <!-- NAMA & ICON -->
                            <td class="py-3.5 pr-3">
                                <div class="flex items-center gap-3">
                                    @if($item->icon === 'droplet' || $item->tipe === 'Minyak jelantah')
                                        <!-- Droplet Icon: Amber -->
                                        <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"/>
                                            </svg>
                                        </div>
                                    @elseif($item->icon === 'cup')
                                        <!-- Cup Icon: Gray/Slate -->
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M17 8h1a4 4 0 1 1 0 8h-1"/>
                                                <path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/>
                                                <line x1="6" x2="6" y1="2" y2="4"/>
                                                <line x1="10" x2="10" y1="2" y2="4"/>
                                                <line x1="14" x2="14" y1="2" y2="4"/>
                                            </svg>
                                        </div>
                                    @else
                                        <!-- Bottle Icon: Blue -->
                                        <div class="w-8 h-8 rounded-full bg-sky-50 text-sky-700 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M10 2h4"/>
                                                <path d="M10 5h4"/>
                                                <path d="M10 2v3"/>
                                                <path d="M14 2v3"/>
                                                <path d="M9 8h6l2 4v8a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2v-8l2-4z"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <div>
                                        <span class="font-semibold text-slate-800 {{ !$item->is_active ? 'text-slate-400' : '' }}">
                                            {{ $item->nama }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- TIPE BADGE -->
                            <td class="py-3.5 px-3">
                                @if($item->tipe === 'Minyak jelantah')
                                    <span class="inline-block bg-[#FEF3C7] text-[#92400E] text-[11px] font-medium px-2.5 py-0.5 rounded-full">
                                        Minyak jelantah
                                    </span>
                                @else
                                    <span class="inline-block bg-[#E0F2FE] text-[#0369A1] text-[11px] font-medium px-2.5 py-0.5 rounded-full">
                                        Botol plastik
                                    </span>
                                @endif
                            </td>

                            <!-- POIN PER KG -->
                            <td class="py-3.5 px-3 text-center">
                                <span class="font-bold text-slate-900 text-sm {{ !$item->is_active ? 'text-slate-400' : '' }}">
                                    {{ $item->poin_saat_ini }}
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td class="py-3.5 px-3 text-center">
                                @if($item->is_active)
                                    <span class="inline-flex items-center gap-1.5 text-emerald-700 font-semibold text-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="text-slate-400 font-normal text-xs">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- AKSI -->
                            <td class="py-3.5 pl-3 text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if($item->is_active)
                                        <a href="{{ route('admin.kategori.index', ['selected' => $item->id]) }}" class="px-3 py-1.5 bg-white border border-slate-300 hover:border-slate-400 text-slate-700 hover:text-slate-900 rounded-lg text-xs font-medium transition shadow-2xs {{ $isSelected ? 'ring-2 ring-[#14532D] text-[#14532D]' : '' }}">
                                            Ubah tarif
                                        </a>
                                        <!-- Tombol Nonaktifkan -->
                                        <form method="POST" action="{{ route('admin.kategori.toggle-status', $item->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan kategori {{ $item->nama }}? Kategori nonaktif tidak akan muncul di form pengajuan warga.')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg transition" title="Nonaktifkan kategori">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Tombol Aktifkan (persis seperti tombol Aktifkan di gambar untuk Gelas Plastik) -->
                                        <form method="POST" action="{{ route('admin.kategori.toggle-status', $item->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 bg-white border border-slate-300 hover:border-emerald-600 hover:text-emerald-700 text-slate-700 rounded-lg text-xs font-medium transition shadow-2xs cursor-pointer">
                                                Aktifkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada kategori daur ulang. Silakan klik "Tambah kategori" untuk memulai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KOLOM KANAN: PANEL UBAH TARIF & RIWAYAT TARIF (5 COLUMNS) -->
        <div class="lg:col-span-5 space-y-6">
            @if($selectedKategori)
            <!-- CARD UBAH TARIF -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                <!-- HEADER UBAH TARIF -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Ubah tarif</h3>
                        <p class="text-xs text-slate-500 font-normal">{{ $selectedKategori->nama }}</p>
                    </div>
                    <span class="bg-[#FEF3C7] text-[#92400E] text-[11px] font-semibold px-2.5 py-1 rounded-full">
                        Saat ini {{ $selectedKategori->poin_saat_ini }} poin/kg
                    </span>
                </div>

                <!-- FORM JADWALKAN TARIF BARU -->
                <form method="POST" action="{{ route('admin.kategori.jadwalkan-tarif', $selectedKategori->id) }}" class="space-y-4">
                    @csrf

                    <!-- INPUT TARIF BARU -->
                    <div>
                        <label for="poin_per_kg" class="block text-xs font-medium text-slate-700 mb-1.5">
                            Tarif baru
                        </label>
                        <div class="flex rounded-xl border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-[#14532D] focus-within:border-transparent transition">
                            <input type="number" name="poin_per_kg" id="poin_per_kg" value="{{ old('poin_per_kg', $selectedKategori->poin_saat_ini) }}" min="1" required class="flex-1 px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none" placeholder="12">
                            <span class="bg-slate-50 border-l border-slate-200 px-3 py-2 text-xs text-slate-500 flex items-center">
                                poin per kg
                            </span>
                        </div>
                    </div>

                    <!-- INPUT BERLAKU MULAI -->
                    <div>
                        <label for="berlaku_mulai" class="block text-xs font-medium text-slate-700 mb-1.5">
                            Berlaku mulai
                        </label>
                        <input type="date" name="berlaku_mulai" id="berlaku_mulai" value="{{ old('berlaku_mulai', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition">
                    </div>

                    <!-- KOTAK INFORMASI PERLINDUNGAN POIN LAMA -->
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3 flex items-start gap-2.5 text-xs text-slate-600 leading-relaxed">
                        <svg class="w-4 h-4 text-slate-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p id="info-notice">
                            Setoran sebelum <span id="label-tanggal" class="font-semibold">{{ \Carbon\Carbon::now()->translatedFormat('j F Y') }}</span> tetap dihitung {{ $selectedKategori->poin_saat_ini }} poin/kg. Poin lama tidak berubah.
                        </p>
                    </div>

                    <!-- TOMBOL JADWALKAN -->
                    <button type="submit" class="w-full bg-[#14532D] hover:bg-[#0f3d21] text-white py-2.5 rounded-xl font-medium text-xs shadow-xs transition cursor-pointer">
                        Jadwalkan tarif baru
                    </button>
                </form>

                <!-- SECTION RIWAYAT TARIF -->
                <div class="pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-900 mb-3">Riwayat tarif</h4>
                    <div class="space-y-3">
                        @forelse($selectedKategori->riwayatTarif as $histori)
                        <div class="flex items-center justify-between py-1">
                            <div>
                                <div class="text-xs font-bold text-slate-900">
                                    {{ $histori->poin_per_kg }} poin/kg
                                </div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $histori->periode_label }}
                                </div>
                            </div>
                            <div>
                                @if($histori->status_label === 'Terjadwal')
                                    <span class="bg-[#FEF3C7] text-[#D97706] text-[10px] font-semibold px-2.5 py-0.5 rounded-full">
                                        Terjadwal
                                    </span>
                                @elseif($histori->status_label === 'Berlaku')
                                    <span class="bg-[#DCFCE7] text-[#15803D] text-[10px] font-semibold px-2.5 py-0.5 rounded-full">
                                        Berlaku
                                    </span>
                                @else
                                    <span class="bg-slate-100 text-slate-500 text-[10px] font-semibold px-2.5 py-0.5 rounded-full">
                                        Selesai
                                    </span>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p class="text-xs text-slate-400 py-2">Belum ada riwayat tarif untuk kategori ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- MODAL TAMBAH KATEGORI BARU -->
<div id="addModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Tambah Kategori Daur Ulang</h3>
            <button type="button" onclick="closeAddModal()" class="text-slate-400 hover:text-slate-700 text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.kategori.store') }}" class="space-y-4">
            @csrf

            <!-- NAMA KATEGORI -->
            <div>
                <label for="nama" class="block text-xs font-medium text-slate-700 mb-1">Nama Kategori</label>
                <input type="text" name="nama" id="nama" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:outline-none" placeholder="Contoh: Kardus Bekas">
            </div>

            <!-- TIPE & SATUAN -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="tipe" class="block text-xs font-medium text-slate-700 mb-1">Tipe</label>
                    <select name="tipe" id="tipe" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:outline-none">
                        <option value="Botol plastik">Botol plastik</option>
                        <option value="Minyak jelantah">Minyak jelantah</option>
                        <option value="Kertas & Karton">Kertas & Karton</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label for="satuan" class="block text-xs font-medium text-slate-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" id="satuan" value="kg" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:outline-none">
                </div>
            </div>

            <!-- IKON -->
            <div>
                <label for="icon" class="block text-xs font-medium text-slate-700 mb-1">Pilihan Ikon</label>
                <select name="icon" id="icon" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:outline-none">
                    <option value="bottle">Botol (Plastik / Wadah)</option>
                    <option value="droplet">Tetesan Minyak / Cairan</option>
                    <option value="cup">Gelas / Cup Plastik</option>
                </select>
            </div>

            <!-- TARIF AWAL -->
            <div>
                <label for="modal_poin_per_kg" class="block text-xs font-medium text-slate-700 mb-1">Tarif Awal (Poin per kg)</label>
                <input type="number" name="poin_per_kg" id="modal_poin_per_kg" min="1" required class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:outline-none" placeholder="10">
            </div>

            <!-- TANGGAL BERLAKU MULAI -->
            <div>
                <label for="berlaku_sejak" class="block text-xs font-medium text-slate-700 mb-1">Berlaku Sejak Tanggal</label>
                <input type="date" name="berlaku_sejak" id="berlaku_sejak" value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:outline-none">
            </div>

            <!-- DESKRIPSI -->
            <div>
                <label for="deskripsi" class="block text-xs font-medium text-slate-700 mb-1">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" id="deskripsi" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:outline-none" placeholder="Panduan kriteria sampah untuk warga..."></textarea>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-medium hover:bg-slate-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-[#14532D] hover:bg-[#0f3d21] text-white rounded-xl text-xs font-medium transition cursor-pointer">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
    }

    function closeAddModal() {
        document.getElementById('addModal').classList.add('hidden');
    }

    // Update dynamically notice box on date change
    const dateInput = document.getElementById('berlaku_mulai');
    const labelTanggal = document.getElementById('label-tanggal');
    if (dateInput && labelTanggal) {
        dateInput.addEventListener('change', function() {
            if (this.value) {
                const parts = this.value.split('-');
                const d = new Date(parts[0], parts[1] - 1, parts[2]);
                const options = { day: 'numeric', month: 'long', year: 'numeric' };
                labelTanggal.textContent = d.toLocaleDateString('id-ID', options);
            }
        });
    }
</script>
@endpush
@endsection
