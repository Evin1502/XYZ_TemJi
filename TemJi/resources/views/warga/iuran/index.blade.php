<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TemJi - Iuran Bulanan Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        [x-cloak] { display: none !important; } 
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex" 
      x-data="{ 
          pakaiPoin: true, 
          metode: 'transfer', 
          fileName: '',
          nominalAwal: {{ isset($tagihanAktif) && $tagihanAktif ? $tagihanAktif->nominal_asal : 20000 }}, 
          diskon: 10000, 
          get total() { return this.pakaiPoin ? Math.max(0, this.nominalAwal - this.diskon) : this.nominalAwal; },
          formatRupiah(val) { return 'Rp ' + new Intl.NumberFormat('id-ID').format(val); }
      }">

    <!-- SIDEBAR NAVIGASI DESKTOP -->
    <aside class="w-64 bg-white border-r border-gray-200 hidden md:flex flex-col justify-between h-screen sticky top-0 p-5 shrink-0 z-20">
        <div class="space-y-6">
            <!-- Logo TemJi -->
            <div class="flex items-center space-x-3 px-2">
                <div class="w-9 h-9 rounded-full bg-emerald-700 flex items-center justify-center text-white font-bold text-lg">
                    T
                </div>
                <div>
                    <h1 class="font-bold text-gray-900 text-base leading-none">TemJi</h1>
                    <span class="text-[11px] text-gray-400 font-medium">Panel Warga</span>
                </div>
            </div>

            <!-- Menu Navigasi Sidebar -->
            <nav class="space-y-1 text-sm font-medium">
                <a href="#" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-xl transition">
                    <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Beranda</span>
                </a>
                <a href="#" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-xl transition">
                    <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Jadwal</span>
                </a>
                <a href="#" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-xl transition">
                    <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Daur Ulang</span>
                </a>
                <a href="#" class="flex items-center space-x-3 bg-emerald-50 text-emerald-800 font-semibold px-3 py-2.5 rounded-xl transition">
                    <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Iuran Bulanan</span>
                </a>
                <a href="#" class="flex items-center space-x-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 px-3 py-2.5 rounded-xl transition">
                    <svg class="w-5 h-5 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Poin Warga</span>
                </a>
            </nav>
        </div>

        <!-- User Profile Card Sidebar Footer -->
        <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                    DL
                </div>
                <div>
                    <h4 class="text-xs font-bold text-gray-900">Dewi Lestari</h4>
                    <p class="text-[11px] text-gray-400">Sukapura</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- AREA UTAMA DESKTOP (MAIN CONTENT) -->
    <main class="flex-1 p-6 md:p-10 max-w-7xl mx-auto space-y-6 overflow-y-auto">
        
        <!-- Header Halaman Desktop -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200/80 pb-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Iuran Bulanan Warga</h1>
                <p class="text-sm text-gray-500 mt-1">Kelola dan selesaikan tagihan iuran wilayah Anda secara online.</p>
            </div>
            <div class="hidden md:flex items-center space-x-3 bg-white px-4 py-2 border border-gray-200 rounded-xl shadow-sm">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs font-medium text-gray-700">Warga Terverifikasi • Sukapura</span>
            </div>
        </div>

        <!-- Alert Notifikasi Sukses / Error -->
        @if(session('success'))
            <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-xl text-sm font-semibold flex justify-between items-center shadow-sm">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-900 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-xl text-sm font-semibold flex justify-between items-center shadow-sm">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-900 font-bold">&times;</button>
            </div>
        @endif

        <!-- LAYOUT GRID DESKTOP -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- KOLOM KIRI (7 SPAN): FORM & CARD TAGIHAN AKTIF -->
            <div class="lg:col-span-7 space-y-5">
                <h2 class="text-base font-bold text-gray-900 flex items-center space-x-2">
                    <span>Tagihan Aktif</span>
                </h2>

                @if(isset($tagihanAktif) && $tagihanAktif)
                    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-6 space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg">Tagihan {{ $tagihanAktif->bulan_tagihan }}</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Jatuh tempo: <span class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($tagihanAktif->jatuh_tempo)->translatedFormat('d M Y') }}</span></p>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold bg-amber-100/80 text-amber-800 rounded-full">
                                {{ $tagihanAktif->status }}
                            </span>
                        </div>

                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Iuran Wilayah {{ $tagihanAktif->wilayah }}</span>
                                <span class="font-semibold text-gray-900">Rp {{ number_format($tagihanAktif->nominal_asal, 0, ',', '.') }}</span>
                            </div>

                            <!-- Toggle Tukar Poin -->
                            <div class="flex items-center justify-between bg-emerald-50/60 border border-emerald-100 p-4 rounded-xl">
                                <div>
                                    <span class="text-sm font-bold text-gray-900 block">Pakai 100 poin</span>
                                    <span class="text-xs text-gray-400">Saldo 180 • Maksimal 50% tagihan</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="text-xs font-semibold text-emerald-700" x-show="pakaiPoin" x-cloak>- Rp 10.000</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="pakai_poin" x-model="pakaiPoin" class="sr-only peer">
                                        <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-700"></div>
                                    </label>
                                </div>
                            </div>

                            <!-- Total Dibayar -->
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <span class="font-bold text-gray-900 text-base">Total yang Harus Dibayar</span>
                                <span class="font-extrabold text-emerald-800 text-2xl" x-text="formatRupiah(total)"></span>
                            </div>
                        </div>

                        <!-- Form Pembayaran -->
                        <form action="{{ route('warga.iuran.bayar', $tagihanAktif->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2">
                            @csrf
                            <input type="hidden" name="metode_pembayaran" :value="metode">
                            <input type="checkbox" name="pakai_poin" class="hidden" :checked="pakaiPoin">
                            
                            <!-- Tab Switching Metode Pembayaran -->
                            <div class="grid grid-cols-2 p-1 bg-gray-100 rounded-xl text-xs font-medium">
                                <button type="button" @click="metode = 'transfer'" :class="metode === 'transfer' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-900'" class="py-2.5 rounded-lg transition text-center">
                                    Transfer Bank
                                </button>
                                <button type="button" @click="metode = 'tunai'" :class="metode === 'tunai' ? 'bg-white text-gray-900 shadow-sm font-semibold' : 'text-gray-500 hover:text-gray-900'" class="py-2.5 rounded-lg transition text-center">
                                    Tunai ke RT
                                </button>
                            </div>

                            <!-- Konten Transfer -->
                            <div x-show="metode === 'transfer'" class="space-y-3" x-cloak>
                                <p class="text-xs text-gray-500">Silakan transfer ke rekening resmi kas RW: <strong class="text-gray-800">[NO. REKENING KAS RW]</strong></p>
                                
                                <label :class="!fileName ? 'border-red-300 bg-red-50/20' : 'border-emerald-500 bg-emerald-50/20'" class="border-2 border-dashed hover:border-emerald-500 rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer transition text-center group">
                                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center mb-2 group-hover:scale-110 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800" x-text="fileName ? fileName : 'Unggah bukti transfer (Wajib)'"></span>
                                    <span class="text-xs text-gray-400 mt-0.5">Format file: JPG, PNG (Maksimal 5 MB)</span>
                                    <input type="file" name="bukti_transfer" class="hidden" :required="metode === 'transfer'" @change="fileName = $event.target.files[0]?.name">
                                </label>

                                @error('bukti_transfer')
                                    <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Konten Tunai -->
                            <div x-show="metode === 'tunai'" class="p-4 bg-amber-50 border border-amber-200/60 rounded-xl text-xs text-amber-800 leading-relaxed" x-cloak>
                                Pembayaran tunai sebesar <strong x-text="formatRupiah(total)"></strong> dapat langsung diserahkan kepada Ketua/Pengurus RT setempat.
                            </div>

                            <!-- Tombol Submit Disabled Jika Belum Upload Bukti Saat Transfer -->
                            <button type="submit" 
                                    :disabled="metode === 'transfer' && !fileName"
                                    :class="(metode === 'transfer' && !fileName) ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-[#1b5e3a] hover:bg-[#14492d] text-white active:scale-[0.99]'"
                                    class="w-full font-semibold py-3.5 px-4 rounded-xl transition shadow-md text-sm tracking-wide">
                                Kirim & Selalu Verifikasi Pembayaran
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm p-8 text-center space-y-3">
                        <div class="w-12 h-12 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-base">Semua Iuran Lunas</h3>
                        <p class="text-xs text-gray-500">Tidak ada tagihan aktif yang perlu dibayar saat ini. Terima kasih atas partisipasi Anda!</p>
                    </div>
                @endif
            </div>

            <!-- KOLOM KANAN (5 SPAN): RIWAYAT PEMBAYARAN LEBAR DESKTOP -->
            <div class="lg:col-span-5 space-y-5">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-gray-900">Riwayat Pembayaran</h2>
                    <a href="#" class="text-xs font-semibold text-emerald-700 hover:underline">Lihat Semua</a>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden divide-y divide-gray-100">
                    @forelse($riwayatTagihan as $riwayat)
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">{{ $riwayat->bulan_tagihan }}</h3>
                                    <p class="text-xs text-gray-400">
                                        Transfer {{ $riwayat->poin_digunakan > 0 ? '• Pakai '.$riwayat->poin_digunakan.' poin' : '' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-gray-900 block">Rp {{ number_format($riwayat->total_dibayar, 0, ',', '.') }}</span>
                                <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Lunas</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">November 2026</h3>
                                    <p class="text-xs text-gray-400">Transfer • Pakai 100 poin</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-gray-900 block">Rp 10.000</span>
                                <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Lunas</span>
                            </div>
                        </div>
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900">Oktober 2026</h3>
                                    <p class="text-xs text-gray-400">Transfer</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-bold text-gray-900 block">Rp 20.000</span>
                                <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">Lunas</span>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </main>

</body>
</html>