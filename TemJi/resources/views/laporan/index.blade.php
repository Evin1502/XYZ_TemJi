<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - TemJi</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-[#f8f9fa] text-gray-800 antialiased flex min-h-screen">

    <!-- SIDEBAR NAVIGASI TEMJI -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between shrink-0 fixed inset-y-0 left-0 z-30">
        <div>
            <!-- Header Brand / Logo -->
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                    T
                </div>
                <div>
                    <h1 class="text-base font-bold text-gray-900 leading-none">TemJi</h1>
                    <p class="text-[11px] text-gray-400 font-medium mt-0.5">Panel Admin</p>
                </div>
            </div>

            <!-- Menu List -->
            <nav class="px-4 py-4 space-y-6 text-sm">
                <!-- Group UTAMA -->
                <div>
                    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Utama</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 font-medium transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 font-medium transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span>Wilayah</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 font-medium transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                                <span>Kategori & Poin</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group OPERASIONAL -->
                <div>
                    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Operasional</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 font-medium transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>Jadwal</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 font-medium transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                                <span>Penjemputan Tambahan</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 font-medium transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                <span>Pengajuan Daur Ulang</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group KEUANGAN -->
                <div>
                    <p class="px-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Keuangan</p>
                    <ul class="space-y-1">
                        <li>
                            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-gray-600 hover:bg-gray-50 font-medium transition">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                <span>Iuran Bulanan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.laporan') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-emerald-50 text-emerald-800 font-semibold transition">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span>Laporan</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- Sidebar Footer Note -->
        <div class="p-4 m-3 bg-gray-50 rounded-xl border border-gray-100">
            <p class="text-[11px] text-gray-400 leading-relaxed text-center">
                TemJi membantu operasional petugas, bukan menggantikannya
            </p>
        </div>
    </aside>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="flex-1 ml-64 flex flex-col min-h-screen">
        
        <!-- Top Navbar -->
        <header class="h-16 bg-white border-b border-gray-200 px-8 flex items-center justify-between sticky top-0 z-20">
            <div class="flex items-center gap-2 text-sm text-gray-600 font-medium">
                <span>Laporan</span>
            </div>
            <!-- Profile Info -->
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-xs font-bold text-gray-900 leading-tight">Admin TemJi</p>
                    <p class="text-[11px] text-gray-400">Pengelola</p>
                </div>
                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-700 text-xs font-bold flex items-center justify-center">
                    AT
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="p-8 space-y-6 max-w-7xl">
            
            <!-- Header Section: Title & Ekspor Button -->
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Laporan</h1>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Rincian per minggu sesuai filter dashboard.</p>
                </div>
                <!-- Tombol Trigger Modal Ekspor -->
                <button onclick="toggleModal(true)" class="inline-flex items-center gap-2 bg-emerald-800 hover:bg-emerald-900 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Ekspor laporan</span>
                </button>
            </div>

            <!-- Tabel Data Laporan (Sesuai Mockup Mockup-PBI-17-v2.png) -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50/70 text-gray-500 uppercase tracking-wider font-semibold">
                            <th class="py-3 px-6">Minggu</th>
                            <th class="py-3 px-6">Total terkumpul</th>
                            <th class="py-3 px-6">Penjemputan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700 font-medium">
                        @foreach ($weeklyData as $row)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-6 font-semibold text-gray-800">{{ $row['minggu'] }}</td>
                            <td class="py-3.5 px-6">{{ $row['total_kg'] }}</td>
                            <td class="py-3.5 px-6">{{ $row['penjemputan'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </main>
    </div>

    <!-- MODAL POP-UP EKSPOR LAPORAN (PBI 17 - SESUAI MOCKUP MOCKUP-PBI-17-V2.PNG) -->
    <div id="exportModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden justify-center items-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 relative border border-gray-100 animate-in fade-in duration-150">
            <!-- Modal Header -->
            <div class="flex justify-between items-start border-b border-gray-100 pb-3 mb-4">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Ekspor laporan</h2>
                    <p class="text-xs text-gray-400 mt-0.5">File mengikuti filter yang sedang aktif</p>
                </div>
                <button type="button" onclick="toggleModal(false)" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
            </div>

            <!-- Form Ekspor -->
            <form id="exportForm" method="GET" action="{{ route('admin.dashboard.export') }}">
                <input type="hidden" name="wilayah" value="{{ $wilayah ?? '' }}">
                <input type="hidden" name="kategori" value="{{ $kategori ?? '' }}">
                <input type="hidden" name="periode" value="{{ $periode ?? 'mingguan' }}">

                <!-- Format Pilihan (CSV & PDF) -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Format</label>
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Pilihan CSV -->
                        <label id="csvCard" onclick="selectFormat('csv')" class="border border-gray-200 rounded-xl p-3 flex items-start gap-2.5 cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="format" value="csv" id="formatCsv" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="font-bold text-xs text-gray-900 block">CSV</span>
                                <span class="text-[11px] text-gray-500 leading-tight">Untuk diolah di Excel</span>
                            </div>
                        </label>
                        <!-- Pilihan PDF -->
                        <label id="pdfCard" onclick="selectFormat('pdf')" class="border-2 border-emerald-600 bg-emerald-50/30 rounded-xl p-3 flex items-start gap-2.5 cursor-pointer transition">
                            <input type="radio" name="format" value="pdf" id="formatPdf" checked class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                            <div>
                                <span class="font-bold text-xs text-gray-900 block">PDF</span>
                                <span class="text-[11px] text-gray-500 leading-tight">Siap cetak, dengan grafik</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Tag Filter Aktif -->
                <div class="mb-4">
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Filter aktif</label>
                    <div class="flex flex-wrap gap-1.5 text-xs">
                        <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-md text-[11px] border border-gray-200">
                            {{ !empty($wilayah) ? ($wilayah == 1 ? 'Sukapura' : 'Sukabirus') : 'Semua wilayah' }}
                        </span>
                        <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-md text-[11px] border border-gray-200">
                            {{ !empty($kategori) ? ($kategori == 1 ? 'Minyak Jelantah' : 'Botol Plastik') : 'Semua kategori' }}
                        </span>
                        <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-md text-[11px] border border-gray-200">
                            7 Sep – 1 Nov 2026
                        </span>
                        <span class="bg-gray-100 text-gray-600 px-2.5 py-1 rounded-md text-[11px] border border-gray-200 capitalize">
                            {{ ucfirst($periode ?? 'mingguan') }}
                        </span>
                    </div>
                </div>

                <!-- Opsi Checklist Laporan -->
                <div class="mb-4 space-y-2">
                    <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Isi laporan</label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                        <input type="checkbox" name="include_ringkasan" value="1" checked class="rounded text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        <span>Ringkasan angka utama</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                        <input type="checkbox" name="include_tren" value="1" checked class="rounded text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        <span>Tren kg terkumpul per minggu</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                        <input type="checkbox" name="include_penjemputan" value="1" checked class="rounded text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        <span>Penjemputan terlaksana</span>
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-700 cursor-pointer">
                        <input type="checkbox" name="include_warga" value="1" class="rounded text-emerald-600 border-gray-300 focus:ring-emerald-500">
                        <span>Rincian per warga</span>
                    </label>
                </div>

                <!-- Preview Nama File Dinamis -->
                <div class="mb-5 bg-gray-50 border border-gray-200/80 rounded-lg p-2.5 flex items-center gap-2 text-xs text-gray-600">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span id="fileNamePreview" class="truncate font-mono text-[11px] text-gray-700">Nama file: temji-laporan-mingguan-2026-09-07_2026-11-01.pdf</span>
                </div>

                <!-- Modal Action Buttons -->
                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" onclick="toggleModal(false)" class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-lg transition border border-gray-200">
                        Batal
                    </button>
                    <button type="submit" id="submitBtn" class="px-4 py-2 bg-emerald-800 hover:bg-emerald-900 text-white text-xs font-semibold rounded-lg shadow-sm transition flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        <span id="submitBtnText">Unduh PDF</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT LOGIC -->
    <script>
        function toggleModal(show) {
            const modal = document.getElementById('exportModal');
            if (show) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }

        function selectFormat(fmt) {
            const isPdf = fmt === 'pdf';
            document.getElementById('formatPdf').checked = isPdf;
            document.getElementById('formatCsv').checked = !isPdf;

            const pdfCard = document.getElementById('pdfCard');
            const csvCard = document.getElementById('csvCard');
            const btnText = document.getElementById('submitBtnText');
            const filePreview = document.getElementById('fileNamePreview');

            if (isPdf) {
                pdfCard.className = "border-2 border-emerald-600 bg-emerald-50/30 rounded-xl p-3 flex items-start gap-2.5 cursor-pointer transition";
                csvCard.className = "border border-gray-200 rounded-xl p-3 flex items-start gap-2.5 cursor-pointer hover:bg-gray-50 transition";
                btnText.innerText = "Unduh PDF";
                filePreview.innerText = "Nama file: temji-laporan-mingguan-2026-09-07_2026-11-01.pdf";
            } else {
                csvCard.className = "border-2 border-emerald-600 bg-emerald-50/30 rounded-xl p-3 flex items-start gap-2.5 cursor-pointer transition";
                pdfCard.className = "border border-gray-200 rounded-xl p-3 flex items-start gap-2.5 cursor-pointer hover:bg-gray-50 transition";
                btnText.innerText = "Unduh CSV";
                filePreview.innerText = "Nama file: temji-laporan-mingguan-2026-09-07_2026-11-01.csv";
            }
        }
    </script>
</body>
</html>
