<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kategori Daur Ulang') - TemJi Panel Admin</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN standalone fallback untuk render instan tanpa npm dev server) -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased min-h-screen flex">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between shrink-0 min-h-screen sticky top-0">
        <div>
            <!-- LOGO & BRAND -->
            <div class="px-6 py-5 flex items-center gap-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-[#14532D] flex items-center justify-center text-white shadow-xs">
                    <!-- Leaf / Seed Icon -->
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 text-lg leading-tight tracking-tight">TemJi</h1>
                    <p class="text-xs text-slate-400 font-medium">Panel Admin</p>
                </div>
            </div>

            <!-- NAVIGATION MENU -->
            <nav class="px-4 py-4 space-y-6 text-sm">
                <!-- SEKSI UTAMA -->
                <div>
                    <span class="px-3 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Utama</span>
                    <ul class="mt-2 space-y-1">
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>
                                </svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span>Wilayah</span>
                            </a>
                        </li>
                        @php
                            $isKategoriActive = request()->routeIs('admin.kategori.*', 'admin.aturan-penukaran.*', 'home');
                            $isVerifikasiActive = request()->routeIs('admin.verifikasi-warga.*');
                            $pendingWargaCount = \Illuminate\Support\Facades\Schema::hasTable('warga')
                                ? \App\Models\Warga::menunggu()->count()
                                : 0;
                        @endphp
                        <li>
                            <a href="{{ route('admin.kategori.index') }}" class="flex items-center gap-3 px-3 {{ $isKategoriActive ? 'py-2.5 rounded-xl bg-[#EBF7EE] text-[#14532D] font-semibold shadow-xs' : 'py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition">
                                <svg class="w-4 h-4 {{ $isKategoriActive ? 'text-[#14532D]' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $isKategoriActive ? '2.2' : '2' }}">
                                    <path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/>
                                </svg>
                                <span>Kategori & Poin</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- SEKSI OPERASIONAL -->
                <div>
                    <span class="px-3 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Operasional</span>
                    <ul class="mt-2 space-y-1">
                        <li>
                            <a href="{{ route('admin.verifikasi-warga.index') }}" class="flex items-center justify-between gap-2 px-3 {{ $isVerifikasiActive ? 'py-2.5 rounded-xl bg-[#EBF7EE] text-[#14532D] font-semibold shadow-xs' : 'py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50' }} transition">
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 {{ $isVerifikasiActive ? 'text-[#14532D]' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $isVerifikasiActive ? '2.2' : '2' }}" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/>
                                    </svg>
                                    <span>Verifikasi Warga</span>
                                </div>
                                @if($pendingWargaCount > 0)
                                    <span class="bg-[#FEF3C7] text-[#92400E] text-[10px] font-bold px-2 py-0.5 rounded-full">
                                        {{ $pendingWargaCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M8 2v4"/><path d="M16 2v4"/>
                                </svg>
                                <span>Jadwal</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="7" cy="18" r="2"/><path d="M15 18H9"/><circle cx="17" cy="18" r="2"/><path d="M16 8h4l3 3v5h-7V8z"/>
                                </svg>
                                <span>Penjemputan Tambahan</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/>
                                </svg>
                                <span>Pengajuan Daur Ulang</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- SEKSI KEUANGAN -->
                <div>
                    <span class="px-3 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Keuangan</span>
                    <ul class="mt-2 space-y-1">
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>
                                </svg>
                                <span>Iuran Bulanan</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/>
                                </svg>
                                <span>Laporan</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- FOOTER CATATAN RESMI TEMJI -->
        <div class="p-4">
            <div class="bg-slate-100 rounded-2xl p-3.5 text-xs text-slate-500 leading-relaxed font-normal">
                TemJi membantu operasional petugas, bukan menggantikannya.
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- TOPBAR -->
        <header class="bg-white border-b border-slate-200/80 px-8 py-3.5 flex items-center justify-between sticky top-0 z-20">
            <!-- BREADCRUMB -->
            <nav class="flex items-center text-xs text-slate-500 font-medium gap-2">
                <span>@yield('breadcrumb_parent', 'Kategori & Poin')</span>
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span class="text-slate-900 font-semibold">@yield('breadcrumb_current', 'Kategori daur ulang')</span>
            </nav>

            <!-- USER PROFILE INFO -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs tracking-wider">
                    AT
                </div>
                <div class="text-right">
                    <div class="text-xs font-semibold text-slate-900 leading-tight">Admin TemJi</div>
                    <div class="text-[11px] text-slate-400 leading-tight">Pengelola</div>
                </div>
            </div>
        </header>

        <!-- FLASH NOTIFICATION ALERTS -->
        <div class="px-8 pt-4">
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs mb-2">
                    <div class="font-semibold mb-1">Terdapat kesalahan pengisian:</div>
                    <ul class="list-disc pl-5 space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- PAGE CONTENT -->
        <main class="flex-1 px-8 py-4">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
