<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun - TemJi</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 CDN standalone -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased min-h-screen flex flex-col justify-between p-4 sm:p-6">

    <div class="w-full max-w-md mx-auto my-auto py-6">

        <!-- LOGO & BRANDING -->
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#14532D] text-white shadow-md mb-3">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                    <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">TemJi</h1>
            <p class="text-xs text-slate-500 mt-1">Sistem Pengelolaan Sampah & Daur Ulang Terintegrasi</p>
            <div class="mt-2.5">
                <span class="inline-block bg-[#EBF7EE] text-[#14532D] text-[11px] font-semibold px-3 py-1 rounded-full border border-emerald-200">
                    Otentikasi Multi-Role (Warga, Petugas & Admin)
                </span>
            </div>
        </div>

        <!-- FLASH NOTIFICATION ALERTS -->
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs flex items-center justify-between mb-4 shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs flex items-center justify-between mb-4 shadow-2xs">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 font-bold">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs mb-4 shadow-2xs">
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- LOGIN CARD -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-7 space-y-5">
            <div>
                <h2 class="text-base font-bold text-slate-900">Masuk ke Akun</h2>
                <p class="text-xs text-slate-500 mt-0.5">Gunakan email dan kata sandi yang telah terdaftar pada sistem TemJi.</p>
            </div>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                <!-- EMAIL -->
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1.5">
                        Alamat Email <span class="text-rose-600">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="contoh: admin@temji.id"
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                    >
                </div>

                <!-- PASSWORD -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-medium text-slate-700">
                            Kata Sandi <span class="text-rose-600">*</span>
                        </label>
                    </div>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        required
                        placeholder="••••••••"
                        class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                    >
                </div>

                <!-- REMEMBER ME -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-[#14532D] focus:ring-[#14532D]">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- SUBMIT BUTTON -->
                <button
                    type="submit"
                    class="w-full bg-[#14532D] hover:bg-[#0f3d21] text-white py-2.5 rounded-xl font-semibold text-xs shadow-xs transition cursor-pointer flex items-center justify-center gap-2"
                >
                    <span>Masuk ke TemJi</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- AKSES CEPAT DEMO MULTI-ROLE -->
            <div class="pt-4 border-t border-slate-100 space-y-2">
                <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block">Akses Cepat Akun Demo (1-Klik)</span>
                <div class="grid grid-cols-3 gap-2">
                    <button
                        type="button"
                        onclick="fillDemo('admin@temji.id', 'password123')"
                        class="px-2.5 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-300 text-slate-700 text-center transition cursor-pointer"
                        title="Masuk sebagai Pengelola Pusat"
                    >
                        <div class="text-[11px] font-bold text-slate-900">Admin</div>
                        <div class="text-[10px] text-slate-400">Pengelola</div>
                    </button>
                    <button
                        type="button"
                        onclick="fillDemo('petugas@temji.id', 'password123')"
                        class="px-2.5 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-amber-50 hover:border-amber-300 text-slate-700 text-center transition cursor-pointer"
                        title="Masuk sebagai Petugas Penjemputan"
                    >
                        <div class="text-[11px] font-bold text-slate-900">Petugas</div>
                        <div class="text-[10px] text-slate-400">Lapangan</div>
                    </button>
                    <button
                        type="button"
                        onclick="fillDemo('warga@temji.id', 'password123')"
                        class="px-2.5 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-sky-50 hover:border-sky-300 text-slate-700 text-center transition cursor-pointer"
                        title="Masuk sebagai Warga Masyarakat"
                    >
                        <div class="text-[11px] font-bold text-slate-900">Warga</div>
                        <div class="text-[10px] text-slate-400">Masyarakat</div>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- FOOTER CATATAN RESMI TEMJI -->
    <footer class="text-center py-4">
        <p class="text-xs text-slate-400 font-normal">
            TemJi membantu operasional petugas, bukan menggantikannya.
        </p>
    </footer>

    <script>
        function fillDemo(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
