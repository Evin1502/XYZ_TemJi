<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Jadwal Penjemputan - TemJi</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN standalone) -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900 antialiased flex justify-center">

    <div class="w-full max-w-[430px] min-h-screen bg-white relative flex flex-col justify-between shadow-lg px-5 py-6">

        <div>
            <!-- HEADER -->
            <div class="flex items-center gap-4 pb-4">
                <a href="{{ route('warga.daur-ulang.create', ['kode' => $pengajuan->kode_pengajuan]) }}"
                   class="w-11 h-11 rounded-2xl border border-slate-200 bg-white flex items-center justify-center text-slate-700 shadow-2xs hover:bg-slate-50 transition cursor-pointer"
                   aria-label="Kembali">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </a>
                <div>
                    <h1 class="text-[19px] font-bold text-slate-900 leading-tight">Pilih jadwal penjemputan</h1>
                    <p class="text-[13px] text-slate-500 font-normal mt-0.5">Tentukan slot waktu penjemputan daur ulang</p>
                </div>
            </div>

            <hr class="border-slate-100 my-2">

            <!-- KARTU INFORMASI PENGAJUAN -->
            <div class="my-4 p-4 rounded-2xl border border-[#A7D7B8] bg-[#F4FAF6] space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Kode Pengajuan</span>
                    <span class="px-2 py-0.5 rounded-full bg-[#E1F3E8] text-[#1B5E3A] text-xs font-bold">{{ $pengajuan->status }}</span>
                </div>
                <div class="text-xl font-extrabold text-[#14532D] tracking-wide">
                    {{ $pengajuan->kode_pengajuan }}
                </div>
                <p class="text-xs text-slate-600 font-medium">
                    {{ $pengajuan->ringkasan_item }}
                </p>
                <div class="pt-2 border-t border-emerald-100 flex items-center justify-between text-xs text-slate-600">
                    <span>Estimasi berat: <strong class="text-slate-900">{{ $pengajuan->total_estimasi_kg }} kg</strong></span>
                    <span>Estimasi poin: <strong class="text-[#14532D]">{{ $pengajuan->total_estimasi_poin }} poin</strong></span>
                </div>
            </div>

            <!-- SLOT JADWAL TERSEDIA -->
            <div class="space-y-3 mt-5">
                <h2 class="text-sm font-bold text-slate-800">Slot yang tersedia di wilayah Anda:</h2>

                <div class="p-4 rounded-2xl border-2 border-[#1B5E3A] bg-[#F4FAF6] flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-sm">Kamis, 6 November 2026</span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-semibold">Tersedia</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Pukul 09.00 – 11.00 WIB (Petugas: Pak Dedi)</p>
                    </div>
                    <div class="w-5 h-5 rounded-full border-4 border-[#1B5E3A] bg-white"></div>
                </div>

                <div class="p-4 rounded-2xl border border-slate-200 bg-white hover:border-slate-300 transition flex items-center justify-between cursor-pointer">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-sm">Sabtu, 8 November 2026</span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-semibold">Tersedia</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Pukul 08.00 – 10.00 WIB (Petugas: Pak Ujang)</p>
                    </div>
                    <div class="w-5 h-5 rounded-full border border-slate-300 bg-white"></div>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTON -->
        <div class="pt-6">
            <a href="{{ route('warga.daur-ulang.create', ['kode' => $pengajuan->kode_pengajuan]) }}"
               class="w-full py-3.5 bg-[#1B5E3A] hover:bg-[#14492C] text-white rounded-xl font-bold text-sm text-center transition block shadow-xs active:scale-98">
                Konfirmasi Jadwal Penjemputan
            </a>
        </div>

    </div>

</body>
</html>
