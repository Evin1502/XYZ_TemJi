<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas Lapangan - TemJi</title>

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

    <!-- NAVBAR PETUGAS -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-600 flex items-center justify-center text-white shadow-xs">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 text-base leading-tight">TemJi Petugas</h1>
                    <p class="text-[11px] text-slate-400 font-medium">Operasional Penjemputan Lapangan</p>
                </div>
            </div>

            <!-- USER MENU & LOGOUT -->
            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-semibold text-slate-900 leading-tight">{{ Auth::user()->name }}</div>
                    <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200">
                        {{ Auth::user()->role_label }}
                    </span>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl border border-slate-200 hover:border-rose-300 hover:bg-rose-50 text-slate-700 hover:text-rose-700 font-medium text-xs transition cursor-pointer flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 py-8 space-y-6">

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-xl text-xs flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-xl text-xs flex items-center justify-between shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 font-bold">&times;</button>
            </div>
        @endif

        <!-- WELCOME BANNER PETUGAS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <span class="inline-block text-[11px] font-semibold uppercase tracking-wider text-amber-700 bg-amber-50 px-2.5 py-0.5 rounded-full mb-1 border border-amber-200">
                    Portal Petugas Lapangan
                </span>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Halo, {{ Auth::user()->name }}</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Anda masuk dengan peran <strong>Petugas Lapangan</strong>. Pantau jadwal rutin penjemputan sampah dan daur ulang warga di Sukapura & Sukabirus.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>Petugas Siaga Tugas</span>
                </span>
            </div>
        </div>

        <!-- OPERASIONAL CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-2">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Rute Penjemputan Rutin</div>
                <div class="text-base font-bold text-slate-900">Wilayah Sukapura & Sukabirus</div>
                <p class="text-xs text-slate-500">Penjemputan terjadwal sampah organik & daur ulang.</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-2">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Identitas Petugas</div>
                <div class="text-base font-bold text-slate-900">{{ Auth::user()->name }}</div>
                <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 space-y-2">
                <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Status Otoritas Peran</div>
                <div class="text-base font-bold text-amber-700">{{ Auth::user()->role_label }}</div>
                <p class="text-xs text-slate-500">Hak akses terbatas pada operasional lapangan.</p>
            </div>
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
