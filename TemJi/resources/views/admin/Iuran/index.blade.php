<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TemJi - Iuran Bulanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100/60 text-gray-800 antialiased flex min-h-screen">

    <!-- SIDEBAR LEFT -->
    <aside class="w-64 bg-white border-r border-gray-200/80 flex flex-col justify-between p-4 min-h-screen shrink-0">
        <div>
            <!-- Logo TemJi -->
            <div class="flex items-center space-x-3 px-3 py-2 mb-6">
                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.05 2.57c-2.4 0-4.73.95-6.43 2.65L4 11.83C2.3 13.53 1.35 15.86 1.35 18.26c0 1.35.3 2.65.9 3.82.2.4.6.65 1.05.65h15.4c.45 0 .85-.25 1.05-.65.6-1.17.9-2.47.9-3.82 0-2.4-.95-4.73-2.65-6.43l-6.62-6.61c-1.7-1.7-4.03-2.65-6.43-2.65z"/></svg>
                </div>
                <div>
                    <h1 class="font-bold text-gray-900 text-base leading-none">TemJi</h1>
                    <span class="text-xs text-gray-400">Panel Admin</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="space-y-6">
                <!-- Group UTAMA -->
                <div>
                    <div class="px-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Utama</div>
                    <ul class="space-y-1 text-sm font-medium">
                        <li>
                            <a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Wilayah</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 11h.01M7 15h.01M13 7h.01M13 11h.01M13 15h.01M17 7h.01M17 11h.01M17 15h.01"/></svg>
                                <span>Kategori & Poin</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group OPERASIONAL -->
                <div>
                    <div class="px-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Operasional</div>
                    <ul class="space-y-1 text-sm font-medium">
                        <li>
                            <a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Jadwal</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4 4 4-4m-4-5v9"/></svg>
                                <span>Penjemputan Tambahan</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                <span>Pengajuan Daur Ulang</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group KEUANGAN -->
                <div>
                    <div class="px-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Keuangan</div>
                    <ul class="space-y-1 text-sm font-medium">
                        <li>
                            <a href="#" class="flex items-center space-x-3 px-3 py-2 bg-emerald-50 text-emerald-800 font-semibold rounded-xl transition">
                                <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Iuran Bulanan</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Laporan</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Footer Banner -->
        <div class="bg-gray-100/70 rounded-xl p-3 text-xs text-gray-500 leading-relaxed">
            TemJi membantu operasional petugas, bukan menggantikannya.
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 p-8 overflow-y-auto">
        <!-- Topbar / Breadcrumb & Profile -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <span>Keuangan</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 5l7 7-7 7"/></svg>
                <span class="font-semibold text-gray-800">Iuran bulanan</span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-700 font-bold flex items-center justify-center text-xs">
                    AT
                </div>
                <div class="text-right">
                    <div class="text-sm font-semibold text-gray-900 leading-tight">Admin TemJi</div>
                    <div class="text-xs text-gray-400">Pengelola</div>
                </div>
            </div>
        </div>

        <!-- Page Title & Tabs -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Iuran bulanan</h1>
                <p class="text-sm text-gray-500 mt-1">Tetapkan nominal tiap wilayah, lalu terbitkan tagihan sekali per bulan.</p>
            </div>
            <!-- Tab Buttons -->
            <div class="inline-flex p-1 bg-gray-200/50 rounded-xl border border-gray-200/60">
                <button class="px-4 py-1.5 text-sm font-medium text-gray-900 bg-white rounded-lg shadow-sm">
                    Nominal & tagihan
                </button>
                <button class="px-4 py-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 transition">
                    Pembayaran
                </button>
            </div>
        </div>

        <!-- Layout Grid 2:1 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- LEFT COLUMN (2/3) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Table 1: Nominal iuran per wilayah -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900 text-base">Nominal iuran per wilayah</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Perubahan nominal berlaku untuk tagihan berikutnya</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs text-gray-400 bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="py-3 px-5 font-normal">Wilayah</th>
                                    <th class="py-3 px-5 font-normal">Nominal per bulan</th>
                                    <th class="py-3 px-5 font-normal">Rumah tertagih</th>
                                    <th class="py-3 px-5 font-normal">Berlaku sejak</th>
                                    <th class="py-3 px-5 text-right font-normal"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-800">
                                @if(isset($listIuran) && count($listIuran) > 0)
                                    @foreach($listIuran as $item)
                                        <tr>
                                            <td class="py-4 px-5 font-medium">{{ $item->wilayah }}</td>
                                            <td class="py-4 px-5 font-bold">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                            <td class="py-4 px-5 text-gray-600">{{ $item->rumah_tertagih ?? '312' }}</td>
                                            <td class="py-4 px-5 text-gray-500">{{ \Carbon\Carbon::parse($item->berlaku_sejak)->translatedFormat('F Y') }}</td>
                                            <td class="py-4 px-5 text-right">
                                                <button class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                                    Ubah
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="py-4 px-5 font-semibold text-gray-900">Sukapura</td>
                                        <td class="py-4 px-5 font-bold text-gray-900">Rp 20.000</td>
                                        <td class="py-4 px-5 text-gray-600">312</td>
                                        <td class="py-4 px-5 text-gray-500">Juli 2026</td>
                                        <td class="py-4 px-5 text-right">
                                            <button class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                                Ubah
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 px-5 font-semibold text-gray-900">Sukabirus</td>
                                        <td class="py-4 px-5 font-bold text-gray-900">Rp 15.000</td>
                                        <td class="py-4 px-5 text-gray-600">254</td>
                                        <td class="py-4 px-5 text-gray-500">Juli 2026</td>
                                        <td class="py-4 px-5 text-right">
                                            <button class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition">
                                                Ubah
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Table 2: Tagihan terbit -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="font-bold text-gray-900 text-base">Tagihan terbit</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="text-xs text-gray-400 bg-gray-50/50 border-b border-gray-100">
                                <tr>
                                    <th class="py-3 px-5 font-normal">Bulan</th>
                                    <th class="py-3 px-5 font-normal">Wilayah</th>
                                    <th class="py-3 px-5 font-normal">Jumlah tagihan</th>
                                    <th class="py-3 px-5 font-normal">Total nominal</th>
                                    <th class="py-3 px-5 font-normal">Jatuh tempo</th>
                                    <th class="py-3 px-5 font-normal">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-gray-800">
                                <tr>
                                    <td class="py-4 px-5 font-bold text-gray-900">Oktober 2026</td>
                                    <td class="py-4 px-5 text-gray-600">Sukapura</td>
                                    <td class="py-4 px-5 text-gray-600">312</td>
                                    <td class="py-4 px-5 font-medium">Rp 6.240.000</td>
                                    <td class="py-4 px-5 text-gray-500">10 Okt 2026</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                            Terbit
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-4 px-5 font-bold text-gray-900">Oktober 2026</td>
                                    <td class="py-4 px-5 text-gray-600">Sukabirus</td>
                                    <td class="py-4 px-5 text-gray-600">254</td>
                                    <td class="py-4 px-5 font-medium">Rp 3.810.000</td>
                                    <td class="py-4 px-5 text-gray-500">10 Okt 2026</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">
                                            Terbit
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-4 px-5 font-bold text-gray-900">September 2026</td>
                                    <td class="py-4 px-5 text-gray-600">Semua wilayah</td>
                                    <td class="py-4 px-5 text-gray-600">560</td>
                                    <td class="py-4 px-5 font-medium">Rp 9.950.000</td>
                                    <td class="py-4 px-5 text-gray-500">10 Sep 2026</td>
                                    <td class="py-4 px-5">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            Ditutup
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (1/3): Form Buat Tagihan -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5 sticky top-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-gray-900 text-base">Buat tagihan bulanan</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Belum terbit untuk November</p>
                        </div>
                        <span class="px-2.5 py-0.5 text-xs font-semibold bg-amber-50 text-amber-700 rounded-md">
                            Draf
                        </span>
                    </div>

                    <form action="{{ route('admin.iuran.store') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <!-- Select Bulan -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bulan tagihan</label>
                            <div class="relative">
                                <select name="bulan" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-800 appearance-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none pr-8">
                                    <option value="2026-11">November 2026</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox List Wilayah -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Wilayah</label>
                            <div class="space-y-2.5 text-sm">
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center space-x-2.5 text-gray-800 cursor-pointer">
                                        <input type="checkbox" name="wilayah[]" value="Sukapura" checked class="w-4 h-4 rounded text-emerald-700 focus:ring-emerald-500 border-gray-300">
                                        <span class="font-medium">Sukapura</span>
                                    </label>
                                    <span class="text-gray-400 text-xs">312 × Rp 20.000</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center space-x-2.5 text-gray-800 cursor-pointer">
                                        <input type="checkbox" name="wilayah[]" value="Sukabirus" checked class="w-4 h-4 rounded text-emerald-700 focus:ring-emerald-500 border-gray-300">
                                        <span class="font-medium">Sukabirus</span>
                                    </label>
                                    <span class="text-gray-400 text-xs">254 × Rp 15.000</span>
                                </div>
                            </div>
                        </div>

                        <!-- Input Jatuh Tempo -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jatuh tempo</label>
                            <input type="text" name="jatuh_tempo" value="10 November 2026" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-800 focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>

                        <!-- Total Summary Box -->
                        <div class="p-4 bg-gray-50/80 rounded-xl space-y-2 text-sm border border-gray-100">
                            <div class="flex justify-between items-center text-gray-500">
                                <span>Jumlah tagihan</span>
                                <span class="font-semibold text-gray-900">566</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-500">
                                <span>Total nominal</span>
                                <span class="font-bold text-gray-900 text-base">Rp 10.050.000</span>
                            </div>
                        </div>

                        <p class="text-[11px] text-gray-400 leading-relaxed">
                            Satu tagihan per rumah per bulan. Sistem menolak tagihan ganda.
                        </p>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-[#1b5e3a] hover:bg-[#14492d] text-white font-medium py-3 px-4 rounded-xl transition duration-200 shadow-sm text-sm">
                            Terbitkan 566 tagihan
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>

</body>
</html>