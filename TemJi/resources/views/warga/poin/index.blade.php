<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poin Saya - TemJi Warga</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 Standalone CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#EEF2F0] text-slate-800 antialiased min-h-screen flex justify-center items-start sm:py-6">

    <!-- MOBILE CARD CONTAINER (Presisi Mockup UI Mobile TemJi) -->
    <div class="w-full max-w-md bg-[#F8FAF8] min-h-screen sm:min-h-[844px] sm:rounded-3xl shadow-2xl relative flex flex-col justify-between overflow-hidden border border-slate-200/80">

        <!-- TOP BAR / HEADER -->
        <header class="px-5 pt-6 pb-2">
            <div class="flex items-center justify-between gap-3 mb-2">
                <div class="flex items-center gap-2">
                    <a href="{{ route('warga.dashboard') }}" class="w-8 h-8 rounded-full bg-white border border-slate-200/80 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition shadow-2xs" title="Kembali ke Dashboard">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <span class="text-[11px] font-bold text-emerald-800 bg-emerald-100/70 px-2.5 py-0.5 rounded-full border border-emerald-200">
                        Portal Warga TemJi
                    </span>
                </div>

                <!-- SWITCHER WARGA DEMO (Untuk Pengujian Multi-Konteks & Dosen Penguji) -->
                <div class="relative">
                    <button type="button" onclick="toggleSwitcher()" class="text-[11px] font-semibold text-slate-600 hover:text-emerald-700 bg-white hover:bg-slate-50 px-2.5 py-1 rounded-xl border border-slate-200 transition shadow-2xs flex items-center gap-1 cursor-pointer">
                        <span>Ganti Warga</span>
                        <svg class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- DROPDOWN SWITCHER -->
                    <div id="switcherDropdown" class="hidden absolute right-0 mt-1 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-40 text-xs">
                        <div class="font-bold text-slate-400 text-[10px] uppercase px-2 py-1">Pilih Akun Uji</div>
                        @forelse($daftarWargaDemo as $dw)
                            <a href="{{ url()->current() }}?warga_id={{ $dw->id }}" class="flex items-center justify-between px-2.5 py-2 rounded-xl hover:bg-emerald-50 transition {{ ($warga && $warga->id == $dw->id) ? 'bg-emerald-50 text-emerald-900 font-bold' : 'text-slate-700' }}">
                                <div class="truncate">
                                    <div class="truncate">{{ $dw->nama_lengkap }}</div>
                                    <div class="text-[10px] text-slate-400 font-normal">{{ $dw->wilayah?->nama ?? 'Tanpa Wilayah' }}</div>
                                </div>
                                <span class="text-[11px] font-bold text-emerald-700 shrink-0">{{ $dw->saldo_poin }} p</span>
                            </a>
                        @empty
                            <div class="px-2 py-2 text-slate-400 text-center">Tidak ada warga lain.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- TITLE & CITIZEN NAME -->
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight leading-tight">Poin saya</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5 flex items-center gap-1.5">
                    <span class="font-semibold text-slate-700">{{ $warga->nama_lengkap ?? 'Dewi Lestari' }}</span>
                    <span class="text-slate-300">&middot;</span>
                    <span>{{ $warga->wilayah?->nama ?? 'Sukapura' }}</span>
                </p>
            </div>
        </header>

        <!-- MAIN SCROLLABLE CONTENT -->
        <main class="flex-1 px-5 pt-2 pb-24 space-y-4 overflow-y-auto">

            <!-- KARTU SALDO POIN (HIJAU TUA #14532D) -->
            <div class="bg-[#14532D] rounded-3xl p-5 sm:p-6 text-white shadow-md relative overflow-hidden">
                <!-- Background Subtle Watermark Leaf -->
                <div class="absolute -right-6 -bottom-8 w-36 h-36 opacity-10 pointer-events-none text-white">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.4z"/>
                    </svg>
                </div>

                <!-- SALDO POIN -->
                <div class="relative z-10">
                    <span class="text-xs uppercase tracking-wider text-emerald-200/90 font-medium">Saldo poin</span>
                    <div class="flex items-baseline gap-1.5 mt-1">
                        <span class="text-4xl sm:text-5xl font-extrabold tracking-tight text-white">{{ number_format($saldoPoin, 0, ',', '.') }}</span>
                        <span class="text-lg sm:text-xl font-medium text-emerald-200">poin</span>
                    </div>

                    <!-- DIVIDER LINE -->
                    <div class="border-t border-emerald-800/80 my-4"></div>

                    <!-- SETARA POTONGAN IURAN -->
                    <div class="flex items-center justify-between text-xs sm:text-sm">
                        <span class="text-emerald-200/90 font-medium">Setara potongan iuran</span>
                        <span class="text-white font-bold text-base sm:text-lg tracking-tight">Rp {{ number_format($setaraRupiah, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- KARTU PANDUAN POIN -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/70 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">Panduan poin</h2>
                    <span class="text-[11px] text-slate-400 font-medium">per kg ditimbang</span>
                </div>

                <!-- LIST KATEGORI DAUR ULANG (HANYA AKTIF) -->
                <div class="divide-y divide-slate-100">
                    @forelse($daftarKategori as $kat)
                        <div class="py-2.5 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                            <div class="flex items-center gap-3">
                                <!-- ICON KATEGORI -->
                                @if(str_contains(strtolower($kat->nama), 'minyak'))
                                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 border border-amber-200/60 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                    </div>
                                @elseif(str_contains(strtolower($kat->nama), 'berwarna'))
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-200/60 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 border border-sky-200/60 flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                @endif

                                <div>
                                    <h3 class="font-semibold text-slate-800 text-xs sm:text-sm leading-tight">{{ $kat->nama }}</h3>
                                    <span class="text-[10px] text-slate-400">Daur ulang terpilah</span>
                                </div>
                            </div>

                            <!-- POIN PER KG -->
                            <div class="text-right">
                                <span class="font-extrabold text-slate-900 text-sm sm:text-base">{{ $kat->poin_saat_ini }}</span>
                                <span class="text-[11px] text-slate-500 font-medium ml-0.5">poin</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-slate-400">Belum ada kategori aktif.</div>
                    @endforelse
                </div>

                <!-- NOTICE KOTAK INFORMASI -->
                <div class="bg-amber-50/90 border border-amber-200/70 rounded-xl p-3 flex items-start gap-2.5 text-xs text-amber-900 mt-2">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <circle cx="12" cy="12" r="9" stroke-width="2"/>
                        <line x1="12" y1="16" x2="12" y2="12" stroke-width="2"/>
                        <line x1="12" y1="8" x2="12.01" y2="8" stroke-width="2"/>
                    </svg>
                    <p class="leading-relaxed text-[11px] sm:text-xs">
                        <strong>Poin hanya dari daur ulang.</strong> Sampah biasa dan bayar iuran tidak menambah poin.
                    </p>
                </div>
            </div>

            <!-- KARTU RIWAYAT POIN -->
            <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-xs border border-slate-200/70 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 tracking-tight">Riwayat poin</h2>
                    <button type="button" onclick="openModalRiwayat()" class="text-xs text-emerald-700 hover:text-emerald-800 font-bold hover:underline transition cursor-pointer">
                        Lihat semua
                    </button>
                </div>

                <!-- LIST TRANSAKSI RINGKAS (5 TERAKHIR) -->
                <div class="divide-y divide-slate-100">
                    @forelse($riwayatPoinRingkas as $r)
                        <div class="py-2.5 flex items-center justify-between gap-3 first:pt-0 last:pb-0">
                            <div class="truncate">
                                <h3 class="font-semibold text-slate-900 text-xs sm:text-sm leading-tight truncate">{{ $r->judul }}</h3>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5 truncate">{{ $r->keterangan }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-bold text-sm sm:text-base {{ $r->badge_color }}">
                                    {{ $r->formatted_poin }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">
                            Belum ada riwayat transaksi poin.
                        </div>
                    @endforelse
                </div>
            </div>

        </main>

        <!-- BOTTOM NAVIGATION BAR (5 TAB SESUAI MOCKUP) -->
        <nav class="absolute bottom-0 inset-x-0 bg-white border-t border-slate-200/80 px-4 py-2 z-30 flex items-center justify-around shadow-lg">
            <!-- TAB 1: BERANDA -->
            <a href="{{ route('warga.dashboard') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-700 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-[10px] font-medium">Beranda</span>
            </a>

            <!-- TAB 2: JADWAL -->
            <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-700 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="text-[10px] font-medium">Jadwal</span>
            </a>

            <!-- TAB 3: POIN (ACTIVE TAB - EMERALD #14532D) -->
            <a href="{{ route('warga.poin.index') }}" class="flex flex-col items-center gap-1 text-emerald-800 transition">
                <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-900 flex items-center justify-center shadow-2xs">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"/>
                    </svg>
                </div>
                <span class="text-[10px] font-extrabold text-emerald-900">Poin</span>
            </a>

            <!-- TAB 4: IURAN -->
            <a href="#" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-700 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                </svg>
                <span class="text-[10px] font-medium">Iuran</span>
            </a>

            <!-- TAB 5: AKUN -->
            <a href="{{ route('warga.dashboard') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-slate-700 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[10px] font-medium">Akun</span>
            </a>
        </nav>

        <!-- MODAL INTERAKTIF: LIHAT SEMUA RIWAYAT POIN -->
        <div id="modalRiwayat" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4">
            <div class="bg-white w-full max-w-md max-h-[85vh] rounded-t-3xl sm:rounded-3xl shadow-2xl flex flex-col overflow-hidden animate-in fade-in duration-200">
                
                <!-- HEADER MODAL -->
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Riwayat Mutasi Poin</h2>
                        <p class="text-[11px] text-slate-400">{{ $warga->nama_lengkap ?? 'Dewi Lestari' }} &middot; Saldo {{ number_format($saldoPoin, 0, ',', '.') }} poin</p>
                    </div>
                    <button type="button" onclick="closeModalRiwayat()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition cursor-pointer font-bold">
                        &times;
                    </button>
                </div>

                <!-- FILTER TABS MODAL (Semua / Masuk / Keluar) -->
                <div class="px-5 pt-3 pb-2 flex items-center gap-2 border-b border-slate-100 bg-slate-50/50">
                    <button type="button" onclick="filterRiwayat('semua')" id="tabSemua" class="tab-btn px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-800 text-white transition shadow-2xs">
                        Semua ({{ $riwayatPoinSemua->count() }})
                    </button>
                    <button type="button" onclick="filterRiwayat('masuk')" id="tabMasuk" class="tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition">
                        Poin Masuk ({{ $riwayatPoinSemua->where('jenis', 'masuk')->count() }})
                    </button>
                    <button type="button" onclick="filterRiwayat('keluar')" id="tabKeluar" class="tab-btn px-3 py-1.5 rounded-xl text-xs font-semibold bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 transition">
                        Poin Keluar ({{ $riwayatPoinSemua->where('jenis', 'keluar')->count() }})
                    </button>
                </div>

                <!-- DAFTAR TRANSAKSI LENGKAP -->
                <div class="flex-1 overflow-y-auto px-5 py-3 divide-y divide-slate-100">
                    @forelse($riwayatPoinSemua as $item)
                        <div class="py-3 flex items-center justify-between gap-3 riwayat-item" data-jenis="{{ $item->jenis }}">
                            <div>
                                <div class="font-semibold text-slate-900 text-xs sm:text-sm leading-tight">{{ $item->judul }}</div>
                                <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                    <span>{{ $item->keterangan }}</span>
                                    <span>&bull;</span>
                                    <span>Saldo: {{ number_format($item->saldo_akhir, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-bold text-sm sm:text-base {{ $item->badge_color }}">
                                    {{ $item->formatted_poin }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">Belum ada catatan riwayat poin.</div>
                    @endforelse
                </div>

                <!-- FOOTER MODAL -->
                <div class="p-4 border-t border-slate-100 bg-slate-50 text-center">
                    <button type="button" onclick="closeModalRiwayat()" class="w-full py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold text-xs transition cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- SCRIPT LOGIC INTERAKTIF -->
    <script>
        function toggleSwitcher() {
            const dropdown = document.getElementById('switcherDropdown');
            dropdown.classList.toggle('hidden');
        }

        function openModalRiwayat() {
            document.getElementById('modalRiwayat').classList.remove('hidden');
        }

        function closeModalRiwayat() {
            document.getElementById('modalRiwayat').classList.add('hidden');
        }

        function filterRiwayat(jenis) {
            const items = document.querySelectorAll('.riwayat-item');
            const tabs = document.querySelectorAll('.tab-btn');

            tabs.forEach(tab => {
                tab.classList.remove('bg-emerald-800', 'text-white');
                tab.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200');
            });

            if (jenis === 'semua') {
                document.getElementById('tabSemua').classList.add('bg-emerald-800', 'text-white');
                document.getElementById('tabSemua').classList.remove('bg-white', 'text-slate-600');
                items.forEach(el => el.classList.remove('hidden'));
            } else {
                const activeTab = jenis === 'masuk' ? document.getElementById('tabMasuk') : document.getElementById('tabKeluar');
                activeTab.classList.add('bg-emerald-800', 'text-white');
                activeTab.classList.remove('bg-white', 'text-slate-600');
                items.forEach(el => {
                    if (el.getAttribute('data-jenis') === jenis) {
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                });
            }
        }

        // Tutup dropdown saat klik di luar
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('switcherDropdown');
            if (!e.target.closest('#switcherDropdown') && !e.target.closest('button[onclick="toggleSwitcher()"]')) {
                dropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
