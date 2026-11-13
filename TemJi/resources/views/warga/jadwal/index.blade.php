<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal & Penjemputan Tambahan - TemJi</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 CDN standalone -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    @use('App\Models\JadwalPenjemputan')

    <!-- NAVBAR WARGA -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between gap-4">
            <a href="{{ route('warga.dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-600 flex items-center justify-center text-white shadow-xs">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 text-base leading-tight">Portal Warga TemJi</h1>
                    <p class="text-[11px] text-slate-400 font-medium">Layanan Sampah & Daur Ulang</p>
                </div>
            </a>

            <!-- USER MENU & LOGOUT -->
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-semibold text-slate-900 leading-tight">{{ Auth::user()->name }}</div>
                    <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-sky-50 text-sky-800 border border-sky-200">
                        {{ Auth::user()->role_label }}
                    </span>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl border border-slate-200 hover:border-rose-300 hover:bg-rose-50 text-slate-700 hover:text-rose-700 font-medium text-xs transition cursor-pointer">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-8 space-y-6">

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-xl text-xs font-medium">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-xl text-xs">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs">
                <div class="font-semibold mb-1">Terdapat kesalahan pengisian:</div>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- HEADER HALAMAN -->
        <div>
            <a href="{{ route('warga.dashboard') }}" class="text-xs font-medium text-sky-700 hover:underline">&larr; Kembali ke beranda</a>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Jadwal penjemputan</h2>
            <p class="mt-1 text-sm text-slate-500">
                @if($wilayah)
                    Jadwal rutin mingguan di wilayah <span class="font-semibold text-slate-700">{{ $wilayah->nama }}</span>. Sampah menumpuk sebelum jadwal berikutnya? Ajukan penjemputan tambahan.
                @else
                    Wilayah Anda belum terdaftar. Hubungi admin agar jadwal wilayah Anda bisa ditampilkan.
                @endif
            </p>
        </div>

        <!-- JADWAL RUTIN WILAYAH -->
        <section class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-900">Jadwal rutin wilayah</h3>
                <div class="flex items-center gap-2 text-xs font-medium">
                    <span class="px-3 py-1 rounded-full bg-[#EBF7EE] text-[#14532D]">Sampah biasa</span>
                    <span class="px-3 py-1 rounded-full bg-[#E8F0FB] text-[#1E4E8C]">Daur ulang</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <div class="grid grid-cols-6 min-w-[720px] divide-x divide-slate-100">
                    @foreach(JadwalPenjemputan::HARI as $hari => $label)
                        <div class="min-h-[9rem] p-2.5 space-y-2">
                            <div class="px-1 pb-1 text-xs font-medium text-slate-500">{{ $label }}</div>

                            @forelse($jadwalPerHari->get($hari, collect()) as $jadwal)
                                <div class="rounded-xl px-2.5 py-2 {{ $jadwal->isDaurUlang() ? 'bg-[#E8F0FB] text-[#1E4E8C]' : 'bg-[#EBF7EE] text-[#14532D]' }}">
                                    <div class="text-sm font-bold whitespace-nowrap">{{ $jadwal->rentang_waktu }}</div>
                                    <div class="mt-0.5 text-xs leading-snug">{{ JadwalPenjemputan::JENIS[$jadwal->jenis] }}</div>
                                    <div class="text-xs leading-snug">{{ $jadwal->petugas->name }}</div>
                                </div>
                            @empty
                                <div class="px-1 text-xs text-slate-300">Tidak ada jadwal</div>
                            @endforelse
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-[22rem_minmax(0,1fr)] gap-6 items-start">
            <!-- FORM PENGAJUAN PENJEMPUTAN TAMBAHAN -->
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Ajukan penjemputan tambahan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Di luar jadwal rutin, menunggu persetujuan admin</p>
                </div>

                @if(! $wilayah)
                    <p class="px-6 py-5 text-xs text-slate-500 leading-relaxed">Pengajuan belum bisa dibuat karena wilayah Anda belum terdaftar.</p>
                @elseif($adaMenunggu)
                    <p class="px-6 py-5 text-xs text-slate-500 leading-relaxed">Anda masih punya pengajuan yang menunggu persetujuan. Pengajuan baru bisa dibuat setelah pengajuan itu diproses atau dibatalkan.</p>
                @else
                    <form method="POST" action="{{ route('warga.penjemputan-tambahan.store') }}" class="px-6 py-5 space-y-4">
                        @csrf

                        <div>
                            <label for="tanggal_diminta" class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal penjemputan</label>
                            <input type="date" id="tanggal_diminta" name="tanggal_diminta" required
                                   min="{{ $tanggalMinimal->toDateString() }}" max="{{ $tanggalMaksimal->toDateString() }}"
                                   value="{{ old('tanggal_diminta') }}"
                                   class="w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-600/20 focus:border-sky-600 {{ $errors->has('tanggal_diminta') ? 'border-rose-400' : 'border-slate-200' }}">
                            <p class="mt-1 text-[11px] text-slate-400">Paling cepat besok, paling jauh {{ $tanggalMaksimal->locale('id')->translatedFormat('j F Y') }}.</p>
                        </div>

                        <div>
                            <label for="alasan" class="block text-xs font-semibold text-slate-700 mb-1.5">Alasan</label>
                            <textarea id="alasan" name="alasan" rows="4" required minlength="10" maxlength="500"
                                      placeholder="Contoh: ada acara keluarga, sampah lebih banyak dari biasanya."
                                      class="w-full rounded-xl border bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-sky-600/20 focus:border-sky-600 {{ $errors->has('alasan') ? 'border-rose-400' : 'border-slate-200' }}">{{ old('alasan') }}</textarea>
                        </div>

                        <button type="submit" class="w-full px-5 py-2.5 rounded-xl bg-sky-600 text-sm font-semibold text-white shadow-xs hover:bg-sky-700 transition cursor-pointer">Kirim pengajuan</button>
                    </form>
                @endif
            </section>

            <!-- RIWAYAT PENGAJUAN -->
            <section class="bg-white rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Pengajuan saya</h3>
                </div>

                @forelse($riwayat as $pengajuan)
                    <div class="px-6 py-4 border-b border-slate-100 last:border-b-0 flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0 space-y-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-bold text-slate-900">{{ $pengajuan->kode }}</span>
                                <span class="inline-block text-[11px] font-semibold px-2 py-0.5 rounded-full border {{ $pengajuan->badge_status }}">{{ $pengajuan->status_label }}</span>
                            </div>
                            <div class="text-xs text-slate-500">Diminta untuk {{ $pengajuan->tanggal_diminta->locale('id')->translatedFormat('l, j F Y') }}</div>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $pengajuan->alasan }}</p>
                        </div>

                        @if($pengajuan->isMenunggu())
                            <form method="POST" action="{{ route('warga.penjemputan-tambahan.batal', $pengajuan->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700 transition cursor-pointer">Batalkan</button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-xs text-slate-400">Belum ada pengajuan penjemputan tambahan.</p>
                @endforelse
            </section>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="text-center py-4 border-t border-slate-200/60 bg-white">
        <p class="text-xs text-slate-400 font-normal">
            TemJi membantu operasional petugas, bukan menggantikannya.
        </p>
    </footer>

</body>
</html>
