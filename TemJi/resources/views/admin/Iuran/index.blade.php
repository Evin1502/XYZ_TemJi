<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TemJi - Iuran Bulanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; } </style>
</head>
<body class="bg-gray-100/60 text-gray-800 antialiased flex min-h-screen" x-data="{ openModal: false, selectedId: null, selectedWilayah: '', selectedNominal: 0 }">

    <!-- SIDEBAR LEFT -->
    <aside class="w-64 bg-white border-r border-gray-200/80 flex flex-col justify-between p-4 min-h-screen shrink-0">
        <div>
            <div class="flex items-center space-x-3 px-3 py-2 mb-6">
                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.05 2.57c-2.4 0-4.73.95-6.43 2.65L4 11.83C2.3 13.53 1.35 15.86 1.35 18.26c0 1.35.3 2.65.9 3.82.2.4.6.65 1.05.65h15.4c.45 0 .85-.25 1.05-.65.6-1.17.9-2.47.9-3.82 0-2.4-.95-4.73-2.65-6.43l-6.62-6.61c-1.7-1.7-4.03-2.65-6.43-2.65z"/></svg>
                </div>
                <div>
                    <h1 class="font-bold text-gray-900 text-base leading-none">TemJi</h1>
                    <span class="text-xs text-gray-400">Panel Admin</span>
                </div>
            </div>

            <nav class="space-y-6">
                <div>
                    <div class="px-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Utama</div>
                    <ul class="space-y-1 text-sm font-medium">
                        <li><a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition"><span>Dashboard</span></a></li>
                        <li><a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition"><span>Wilayah</span></a></li>
                        <li><a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition"><span>Kategori & Poin</span></a></li>
                    </ul>
                </div>

                <div>
                    <div class="px-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Operasional</div>
                    <ul class="space-y-1 text-sm font-medium">
                        <li><a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition"><span>Jadwal</span></a></li>
                        <li><a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition"><span>Penjemputan Tambahan</span></a></li>
                        <li><a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition"><span>Pengajuan Daur Ulang</span></a></li>
                    </ul>
                </div>

                <div>
                    <div class="px-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Keuangan</div>
                    <ul class="space-y-1 text-sm font-medium">
                        <li>
                            <a href="#" class="flex items-center space-x-3 px-3 py-2 bg-emerald-50 text-emerald-800 font-semibold rounded-xl transition">
                                <span>Iuran Bulanan</span>
                            </a>
                        </li>
                        <li><a href="#" class="flex items-center space-x-3 px-3 py-2 text-gray-600 hover:bg-gray-50 rounded-xl transition"><span>Laporan</span></a></li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="bg-gray-100/70 rounded-xl p-3 text-xs text-gray-500 leading-relaxed">
            TemJi membantu operasional petugas, bukan menggantikannya.
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 p-8 overflow-y-auto">
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <span>Keuangan</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="9 5l7 7-7 7"/></svg>
                <span class="font-semibold text-gray-800">Iuran bulanan</span>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-700 font-bold flex items-center justify-center text-xs">AT</div>
                <div class="text-right">
                    <div class="text-sm font-semibold text-gray-900 leading-tight">Admin TemJi</div>
                    <div class="text-xs text-gray-400">Pengelola</div>
                </div>
            </div>
        </div>

        <!-- Alert Notification -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-100 border border-emerald-400 text-emerald-800 rounded-xl flex items-center justify-between shadow-sm">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-900 font-bold">&times;</button>
            </div>
        @endif

        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Iuran bulanan</h1>
                <p class="text-sm text-gray-500 mt-1">Tetapkan nominal tiap wilayah, lalu terbitkan tagihan sekali per bulan.</p>
            </div>
            <div class="inline-flex p-1 bg-gray-200/50 rounded-xl border border-gray-200/60">
                <button class="px-4 py-1.5 text-sm font-medium text-gray-900 bg-white rounded-lg shadow-sm">Nominal & tagihan</button>
                <button class="px-4 py-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 transition">Pembayaran</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                                @foreach($listIuran as $item)
                                    <tr>
                                        <td class="py-4 px-5 font-semibold text-gray-900">{{ $item->wilayah }}</td>
                                        <td class="py-4 px-5 font-bold text-gray-900">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                        <td class="py-4 px-5 text-gray-600">{{ $item->rumah_tertagih ?? '300' }}</td>
                                        <td class="py-4 px-5 text-gray-500">{{ \Carbon\Carbon::parse($item->berlaku_sejak)->translatedFormat('F Y') }}</td>
                                        <td class="py-4 px-5 text-right">
                                            <button @click="openModal = true; selectedId = {{ $item->id }}; selectedWilayah = '{{ $item->wilayah }}'; selectedNominal = {{ $item->nominal }}" class="px-3.5 py-1.5 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition active:scale-95">
                                                Ubah
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
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
                                    <td class="py-4 px-5"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Terbit</span></td>
                                </tr>
                                <tr>
                                    <td class="py-4 px-5 font-bold text-gray-900">Oktober 2026</td>
                                    <td class="py-4 px-5 text-gray-600">Sukabirus</td>
                                    <td class="py-4 px-5 text-gray-600">254</td>
                                    <td class="py-4 px-5 font-medium">Rp 3.810.000</td>
                                    <td class="py-4 px-5 text-gray-500">10 Okt 2026</td>
                                    <td class="py-4 px-5"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Terbit</span></td>
                                </tr>
                                <tr>
                                    <td class="py-4 px-5 font-bold text-gray-900">September 2026</td>
                                    <td class="py-4 px-5 text-gray-600">Semua wilayah</td>
                                    <td class="py-4 px-5 text-gray-600">560</td>
                                    <td class="py-4 px-5 font-medium">Rp 9.950.000</td>
                                    <td class="py-4 px-5 text-gray-500">10 Sep 2026</td>
                                    <td class="py-4 px-5"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Ditutup</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Form Buat Tagihan -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5 sticky top-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-gray-900 text-base">Buat tagihan bulanan</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Belum terbit untuk November</p>
                        </div>
                        <span class="px-2.5 py-0.5 text-xs font-semibold bg-amber-50 text-amber-700 rounded-md">Draf</span>
                    </div>

                    <form action="{{ route('admin.iuran.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Bulan tagihan</label>
                            <select name="bulan" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-800 outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="2026-11">November 2026</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">Wilayah</label>
                            <div class="space-y-2.5 text-sm">
                                @foreach($listIuran as $item)
                                    <div class="flex items-center justify-between">
                                        <label class="flex items-center space-x-2.5 text-gray-800 cursor-pointer">
                                            <input type="checkbox" name="wilayah[]" value="{{ $item->wilayah }}" checked class="w-4 h-4 rounded text-emerald-700 focus:ring-emerald-500 border-gray-300">
                                            <span class="font-medium">{{ $item->wilayah }}</span>
                                        </label>
                                        <span class="text-gray-400 text-xs">{{ $item->rumah_tertagih ?? 300 }} × Rp {{ number_format($item->nominal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1.5">Jatuh tempo</label>
                            <input type="text" name="jatuh_tempo" value="10 November 2026" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-800 focus:ring-2 focus:ring-emerald-500 outline-none">
                        </div>

                        <div class="p-4 bg-gray-50/80 rounded-xl space-y-2 text-sm border border-gray-100">
                            <div class="flex justify-between items-center text-gray-500">
                                <span>Jumlah tagihan</span>
                                <span class="font-semibold text-gray-900">{{ $listIuran->sum('rumah_tertagih') ?: 566 }}</span>
                            </div>
                            <div class="flex justify-between items-center text-gray-500">
                                <span>Total nominal</span>
                                <span class="font-bold text-gray-900 text-base">Rp {{ number_format($listIuran->sum(fn($i) => ($i->rumah_tertagih ?? 250) * $i->nominal), 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <p class="text-[11px] text-gray-400 leading-relaxed">
                            Satu tagihan per rumah per bulan. Sistem menolak tagihan ganda.
                        </p>

                        <button type="submit" class="w-full bg-[#1b5e3a] hover:bg-[#14492d] active:scale-[0.99] text-white font-medium py-3 px-4 rounded-xl transition duration-200 shadow-sm text-sm">
                            Terbitkan {{ $listIuran->sum('rumah_tertagih') ?: 566 }} tagihan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <!-- MODAL UBAH NOMINAL -->
    <div x-show="openModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-xl border border-gray-100" @click.away="openModal = false">
            <h3 class="text-lg font-bold text-gray-900 mb-1">Ubah Nominal Iuran</h3>
            <p class="text-xs text-gray-500 mb-4">Wilayah: <span class="font-semibold text-gray-800" x-text="selectedWilayah"></span></p>

            <form :action="'/admin/iuran/' + selectedId" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nominal per bulan (Rp)</label>
                    <input type="number" name="nominal" x-model="selectedNominal" class="w-full px-3.5 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-500 outline-none" required>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button type="button" @click="openModal = false" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Batal</button>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-700 rounded-lg hover:bg-emerald-800">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>