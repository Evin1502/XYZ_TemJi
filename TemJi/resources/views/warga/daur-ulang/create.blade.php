<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Daur Ulang - TemJi</title>

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

    <!-- CONTAINER UTAMA LAYAR HP -->
    <div class="w-full max-w-[430px] min-h-screen bg-white relative flex flex-col justify-between shadow-lg overflow-x-hidden">

        <!-- KONTEN FORMULIR -->
        <div class="px-5 pt-6 pb-28">

            <!-- HEADER -->
            <div class="flex items-center gap-4 pb-4">
                <a href="{{ url('/') }}" class="w-11 h-11 rounded-2xl border border-slate-200 bg-white flex items-center justify-center text-slate-700 shadow-2xs hover:bg-slate-50 transition cursor-pointer" aria-label="Kembali">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </a>
                <div>
                    <h1 class="text-[19px] font-bold text-slate-900 leading-tight">Ajukan daur ulang</h1>
                    <p class="text-[13px] text-slate-500 font-normal mt-0.5">Pastikan sudah dipisah per kategori</p>
                </div>
            </div>

            <hr class="border-slate-100 my-2">

            <!-- PESAN ERROR VALIDASI -->
            @if ($errors->any())
                <div class="my-3 p-3.5 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-xs leading-relaxed">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- JUDUL SEKSI -->
            <div class="mt-4 mb-3">
                <h2 class="text-[15px] font-bold text-slate-800">Apa yang mau disetor?</h2>
            </div>

            <!-- FORM PENGAJUAN -->
            <form id="formPengajuan" action="{{ route('warga.daur-ulang.store') }}" method="POST">
                @csrf

                <!-- LIST KATEGORI DAUR ULANG -->
                <div class="space-y-3">
                    @forelse($kategoriList as $kategori)
                        @php
                            $isDefaultSelected = $defaultSelection[$kategori->id]['selected'] ?? false;
                            $defaultBerat = $defaultSelection[$kategori->id]['berat_kg'] ?? 1;
                        @endphp

                        <div id="card-{{ $kategori->id }}"
                             class="category-card rounded-2xl p-3.5 transition-all cursor-pointer border select-none flex items-center justify-between gap-3 {{ $isDefaultSelected ? 'border-2 border-[#1B5E3A] bg-[#F4FAF6]' : 'border-slate-200 bg-white hover:border-slate-300' }}"
                             onclick="toggleCategory({{ $kategori->id }}, event)">

                            <!-- SISI KIRI: CHECKBOX & IKON & NAMA -->
                            <div class="flex items-center gap-3.5 min-w-0">
                                <!-- CHECKBOX CUSTOM -->
                                <div class="shrink-0">
                                    <input type="checkbox"
                                           name="items[{{ $kategori->id }}][selected]"
                                           id="check-{{ $kategori->id }}"
                                           value="1"
                                           {{ $isDefaultSelected ? 'checked' : '' }}
                                           class="sr-only"
                                           onchange="handleCheckboxChange({{ $kategori->id }})">
                                    <div id="box-{{ $kategori->id }}" class="w-6 h-6 rounded-[7px] flex items-center justify-center transition-colors {{ $isDefaultSelected ? 'bg-[#1B5E3A] text-white' : 'border-[1.5px] border-slate-300 bg-white' }}">
                                        <svg class="w-4 h-4 {{ $isDefaultSelected ? 'block' : 'hidden' }}" id="tick-{{ $kategori->id }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                </div>

                                <!-- LINGKARAN IKON KUSTOM -->
                                <div class="w-12 h-12 rounded-full shrink-0 flex items-center justify-center
                                    @if($kategori->nama === 'Minyak jelantah') bg-[#FDECC8]
                                    @elseif($kategori->nama === 'Botol PET bening') bg-[#DCEBFA]
                                    @else bg-[#EEF0F2] @endif">

                                    @if($kategori->nama === 'Minyak jelantah')
                                        <!-- Ikon Botol Minyak Jelantah Emas -->
                                        <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                                            <!-- Tutup botol -->
                                            <rect x="13" y="4" width="6" height="3" rx="1" fill="#8C531B" />
                                            <!-- Leher botol -->
                                            <path d="M14 7H18V10H14V7Z" fill="#D99B26" />
                                            <!-- Badan botol -->
                                            <rect x="10" y="10" width="12" height="17" rx="3" fill="#E8B038" stroke="#A9711C" stroke-width="1.2" />
                                            <!-- Minyak & tetesan -->
                                            <path d="M11.5 16C13 16 14.5 18 16 18C17.5 18 19 16 20.5 16V24C20.5 25.5 19.5 26 18 26H14C12.5 26 11.5 25.5 11.5 24V16Z" fill="#C98616" opacity="0.6"/>
                                            <path d="M19 19C19 20.5 17.5 21 17.5 21C17.5 21 16 20.5 16 19C16 18 17.5 16.5 17.5 16.5C17.5 16.5 19 18 19 19Z" fill="#A86811" />
                                        </svg>
                                    @elseif($kategori->nama === 'Botol PET bening')
                                        <!-- Ikon Sepasang Botol Plastik Biru Bening -->
                                        <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                                            <!-- Botol belakang -->
                                            <rect x="16.5" y="7" width="3" height="2" rx="0.5" fill="#4B779A" />
                                            <rect x="15" y="9" width="6" height="15" rx="2" fill="#B9D5F0" stroke="#4878A4" stroke-width="1.2" />
                                            <!-- Botol depan miring -->
                                            <rect x="10.5" y="8" width="3" height="2" rx="0.5" fill="#2B5D88" />
                                            <rect x="9" y="10" width="6" height="15" rx="2" fill="#9CC5EC" stroke="#2B5D88" stroke-width="1.2" />
                                            <line x1="9" y1="16" x2="15" y2="16" stroke="#2B5D88" stroke-width="1" />
                                            <line x1="9" y1="19" x2="15" y2="19" stroke="#2B5D88" stroke-width="1" />
                                        </svg>
                                    @else
                                        <!-- Ikon Sepasang Botol Abu-abu Muted -->
                                        <svg class="w-7 h-7" viewBox="0 0 32 32" fill="none">
                                            <rect x="16.5" y="7" width="3" height="2" rx="0.5" fill="#828892" />
                                            <rect x="15" y="9" width="6" height="15" rx="2" fill="#D3D7DC" stroke="#828892" stroke-width="1.2" />
                                            <rect x="10.5" y="8" width="3" height="2" rx="0.5" fill="#6B7280" />
                                            <rect x="9" y="10" width="6" height="15" rx="2" fill="#C5C9CF" stroke="#6B7280" stroke-width="1.2" />
                                            <line x1="9" y1="16" x2="15" y2="16" stroke="#6B7280" stroke-width="1" />
                                            <line x1="9" y1="19" x2="15" y2="19" stroke="#6B7280" stroke-width="1" />
                                        </svg>
                                    @endif
                                </div>

                                <!-- NAMA DAN POIN -->
                                <div class="truncate">
                                    <h3 class="font-bold text-slate-900 text-[14.5px] leading-tight truncate">{{ $kategori->nama }}</h3>
                                    <p class="text-xs text-slate-500 font-normal mt-0.5">{{ $kategori->poin_saat_ini }} poin/kg</p>
                                </div>
                            </div>

                            <!-- SISI KANAN: ESTIMASI BERAT (± X kg) -->
                            <div id="weight-box-{{ $kategori->id }}" class="shrink-0 flex items-center gap-1.5 {{ $isDefaultSelected ? 'flex' : 'hidden' }}" onclick="event.stopPropagation()">
                                @if(!$pengajuanTerkirim)
                                    <button type="button"
                                            onclick="adjustWeight({{ $kategori->id }}, -1)"
                                            class="w-6 h-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-xs font-bold transition cursor-pointer"
                                            title="Kurangi berat">-</button>
                                @endif

                                <span class="font-bold text-slate-900 text-sm whitespace-nowrap min-w-[50px] text-center">
                                    ± <span id="weight-display-{{ $kategori->id }}">{{ $defaultBerat }}</span> kg
                                </span>

                                @if(!$pengajuanTerkirim)
                                    <button type="button"
                                            onclick="adjustWeight({{ $kategori->id }}, 1)"
                                            class="w-6 h-6 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center text-xs font-bold transition cursor-pointer"
                                            title="Tambah berat">+</button>
                                @endif

                                <input type="hidden"
                                       name="items[{{ $kategori->id }}][berat_kg]"
                                       id="weight-input-{{ $kategori->id }}"
                                       value="{{ $defaultBerat }}"
                                       data-poin="{{ $kategori->poin_saat_ini }}">
                            </div>

                        </div>
                    @empty
                        <div class="p-6 bg-slate-50 text-center text-slate-400 rounded-2xl text-xs">
                            Belum ada kategori daur ulang aktif.
                        </div>
                    @endforelse
                </div>

                <!-- INFO DISCLAIMER BOX -->
                <div class="mt-4 p-4 bg-[#F4F6F5] rounded-2xl flex items-start gap-3 text-slate-600 leading-snug">
                    <div class="w-5 h-5 rounded-full border-[1.5px] border-slate-500 flex items-center justify-center shrink-0 mt-0.5 text-slate-600 text-[11px] font-bold">
                        i
                    </div>
                    <p class="text-[12.5px] font-normal text-slate-600">
                        Berat akhir ditimbang petugas. Poin dihitung dari berat timbang, bukan perkiraan.
                    </p>
                </div>

                @if(!$pengajuanTerkirim)
                    <label class="mt-4 mb-24 flex items-start gap-3 text-[13px] text-slate-700">
                        <input type="checkbox" name="sudah_dipisah" value="1" {{ old('sudah_dipisah') ? 'checked' : '' }}
                               class="mt-0.5 h-4 w-4 rounded border-slate-300 accent-[#1B5E3A]">
                        <span>Saya menyatakan daur ulang ini sudah dipisah per kategori.</span>
                    </label>
                @endif

                <!-- STICKY BOTTOM SUBMIT BAR (HANYA AKTIF SAAT PENGISIAN FORM) -->
                @if(!$pengajuanTerkirim)
                    <div class="fixed bottom-0 inset-x-0 max-w-[430px] mx-auto bg-white/95 backdrop-blur-md border-t border-slate-100 px-5 py-3.5 z-30 flex items-center justify-between gap-4">
                        <div>
                            <p class="text-[11px] text-slate-400 font-medium uppercase tracking-wider">Perkiraan Poin</p>
                            <p class="text-base font-extrabold text-[#1B5E3A]" id="totalPoinLabel">0 Poin</p>
                        </div>
                        <button type="submit" class="bg-[#1B5E3A] hover:bg-[#14492C] text-white font-semibold text-sm px-6 py-3 rounded-xl transition shadow-xs cursor-pointer active:scale-98">
                            Ajukan Daur Ulang
                        </button>
                    </div>
                @endif
            </form>
        </div>

        <!-- BOTTOM SHEET MODAL: PENGAJUAN TERKIRIM (TAMPIL SESUAI GAMBAR MOCKUP) -->
        @if($pengajuanTerkirim)
            <!-- BACKDROP OVERLAY GELAP TRANSPARAN (MEMBIARKAN HEADER ATAS TETAP TERLIHAT REDUP) -->
            <div class="fixed inset-0 bg-black/45 z-40 backdrop-blur-[0.5px]"></div>

            <!-- MODAL SHEET CONTAINER -->
            <div class="fixed bottom-0 inset-x-0 max-w-[430px] mx-auto bg-white rounded-t-[32px] px-6 pt-3 pb-7 shadow-2xl z-50">
                <!-- HANDLE BAR -->
                <div class="w-11 h-1.5 bg-slate-300 rounded-full mx-auto mb-5"></div>

                <!-- BADGE CENTANG HIJAU -->
                <div class="w-16 h-16 rounded-full bg-[#E1F3E8] text-[#1B5E3A] flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>

                <!-- JUDUL & KETERANGAN -->
                <h3 class="text-[20px] font-bold text-slate-900 text-center">Pengajuan terkirim</h3>
                <p class="text-[13px] text-slate-500 text-center mt-1 mb-5">
                    Tunjukkan kode ini ke petugas saat penjemputan.
                </p>

                <!-- KOTAK KODE PENGAJUAN BERGARIS PUTUS-PUTUS -->
                <div class="border-[1.5px] border-dashed border-[#A7D7B8] bg-[#F4FAF6] rounded-2xl py-4 px-4 text-center mb-5">
                    <p class="text-xs text-slate-500 font-medium">Kode pengajuan</p>
                    <div id="kodeDisplay" class="text-[28px] font-extrabold text-[#14532D] tracking-wider my-1">
                        {{ $pengajuanTerkirim->kode_pengajuan }}
                    </div>
                    <p class="text-xs text-slate-600 font-medium mt-1 leading-snug">
                        {{ $pengajuanTerkirim->ringkasan_item }}
                    </p>
                </div>

                <!-- DUA TOMBOL AKSI SEJAJAR -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- TOMBOL SALIN KODE -->
                    <button type="button"
                            id="btnSalin"
                            onclick="salinKode('{{ $pengajuanTerkirim->kode_pengajuan }}')"
                            class="w-full py-3 px-4 border border-slate-300 hover:border-slate-400 bg-white hover:bg-slate-50 rounded-xl font-bold text-sm text-slate-800 transition active:scale-98 cursor-pointer flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                            <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                        </svg>
                        <span id="labelSalin">Salin kode</span>
                    </button>

                    <!-- TOMBOL PILIH JADWAL -->
                    <a href="{{ route('warga.daur-ulang.jadwal', $pengajuanTerkirim->kode_pengajuan) }}"
                       class="w-full py-3 px-4 bg-[#1B5E3A] hover:bg-[#14492C] text-white rounded-xl font-bold text-sm text-center transition active:scale-98 shadow-xs block flex items-center justify-center">
                        Pilih jadwal
                    </a>
                </div>

            </div>
        @endif

    </div>

    <!-- JAVASCRIPT LOGIKA INTERAKTIF -->
    <script>
        function toggleCategory(id, event) {
            // Hindari double toggle bila klik berasal langsung dari checkbox atau stepper
            if (event.target.tagName === 'INPUT' || event.target.tagName === 'BUTTON') {
                return;
            }

            const checkbox = document.getElementById('check-' + id);
            checkbox.checked = !checkbox.checked;
            handleCheckboxChange(id);
        }

        function handleCheckboxChange(id) {
            const checkbox = document.getElementById('check-' + id);
            const card = document.getElementById('card-' + id);
            const box = document.getElementById('box-' + id);
            const tick = document.getElementById('tick-' + id);
            const weightBox = document.getElementById('weight-box-' + id);

            if (checkbox.checked) {
                card.className = "category-card rounded-2xl p-3.5 transition-all cursor-pointer border select-none flex items-center justify-between gap-3 border-2 border-[#1B5E3A] bg-[#F4FAF6]";
                box.className = "w-6 h-6 rounded-[7px] flex items-center justify-center transition-colors bg-[#1B5E3A] text-white";
                tick.classList.remove('hidden');
                weightBox.classList.remove('hidden');
                weightBox.classList.add('flex');
            } else {
                card.className = "category-card rounded-2xl p-3.5 transition-all cursor-pointer border select-none flex items-center justify-between gap-3 border-slate-200 bg-white hover:border-slate-300";
                box.className = "w-6 h-6 rounded-[7px] flex items-center justify-center transition-colors border-[1.5px] border-slate-300 bg-white";
                tick.classList.add('hidden');
                weightBox.classList.remove('flex');
                weightBox.classList.add('hidden');
            }

            updateTotalPoin();
        }

        function adjustWeight(id, delta) {
            const input = document.getElementById('weight-input-' + id);
            const display = document.getElementById('weight-display-' + id);

            let current = parseInt(input.value) || 1;
            current = Math.max(1, Math.min(500, current + delta));

            input.value = current;
            display.textContent = current;

            updateTotalPoin();
        }

        function updateTotalPoin() {
            let total = 0;
            const checkboxes = document.querySelectorAll('input[type="checkbox"][id^="check-"]');

            checkboxes.forEach(cb => {
                if (cb.checked) {
                    const id = cb.id.replace('check-', '');
                    const input = document.getElementById('weight-input-' + id);
                    if (input) {
                        const berat = parseInt(input.value) || 0;
                        const poin = parseInt(input.getAttribute('data-poin')) || 0;
                        total += (berat * poin);
                    }
                }
            });

            const label = document.getElementById('totalPoinLabel');
            if (label) {
                label.textContent = total.toLocaleString('id-ID') + ' Poin';
            }
        }

        function salinKode(kode) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(kode).then(() => {
                    tampilkanFeedbackSalin();
                }).catch(() => {
                    fallbackSalin(kode);
                });
            } else {
                fallbackSalin(kode);
            }
        }

        function fallbackSalin(text) {
            const el = document.createElement('textarea');
            el.value = text;
            document.body.appendChild(el);
            el.select();
            document.execCommand('copy');
            document.body.removeChild(el);
            tampilkanFeedbackSalin();
        }

        function tampilkanFeedbackSalin() {
            const label = document.getElementById('labelSalin');
            const original = label.textContent;
            label.textContent = 'Tersalin!';
            setTimeout(() => {
                label.textContent = original;
            }, 2000);
        }

        // Hitung total poin saat pertama kali dibuka
        document.addEventListener('DOMContentLoaded', updateTotalPoin);
    </script>
</body>
</html>
