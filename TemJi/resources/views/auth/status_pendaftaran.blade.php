<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Verifikasi Identitas Warga - TemJi</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- NAVBAR PORTAL WARGA -->
    <header class="bg-white border-b border-slate-200/80 sticky top-0 z-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#14532D] flex items-center justify-center text-white shadow-xs">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 text-lg leading-tight tracking-tight">TemJi</h1>
                    <p class="text-xs text-slate-400 font-medium">Status Verifikasi Identitas</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('warga.registrasi.form') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-medium text-xs transition">
                    + Registrasi Warga Baru
                </a>
                <a href="{{ route('admin.verifikasi-warga.index') }}" class="px-3.5 py-2 rounded-xl bg-[#EBF7EE] hover:bg-emerald-100 text-[#14532D] font-semibold text-xs transition">
                    Panel Admin
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-8 space-y-6">

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-xl text-xs flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-xl text-xs">
                <div class="font-semibold mb-1">Terdapat kesalahan pada berkas pengajuan ulang:</div>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- PENCARIAN STATUS BERDASARKAN NIK -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Cek Status Pendaftaran Warga</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Masukkan 16 digit NIK untuk melihat progres verifikasi identitas akun Anda.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('warga.registrasi.status') }}" class="flex flex-col sm:flex-row gap-3">
                <input
                    type="text"
                    name="nik"
                    value="{{ $searchedNik }}"
                    maxlength="16"
                    placeholder="Masukkan 16 digit NIK terdaftar..."
                    class="flex-1 px-4 py-2.5 border border-slate-300 rounded-xl text-xs font-mono text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                >
                <button type="submit" class="px-5 py-2.5 bg-[#14532D] hover:bg-[#0f3d21] text-white rounded-xl font-semibold text-xs transition cursor-pointer">
                    Periksa Status
                </button>
            </form>

            @if($daftarWargaTerbaru->isNotEmpty())
                <div class="pt-2 border-t border-slate-100 flex flex-wrap items-center gap-2 text-[11px]">
                    <span class="text-slate-400 font-medium">Akses cepat warga terdaftar:</span>
                    @foreach($daftarWargaTerbaru as $sample)
                        <a href="{{ route('warga.registrasi.status', ['warga' => $sample->id]) }}"
                           class="px-2.5 py-1 rounded-full border {{ $warga && $warga->id === $sample->id ? 'bg-[#EBF7EE] border-emerald-300 text-[#14532D] font-semibold' : 'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100' }} transition">
                            {{ $sample->nama_lengkap }} ({{ $sample->badge_status['label'] }})
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        @if($searchedNik !== '' && !$warga)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-center space-y-2">
                <p class="text-sm font-bold text-amber-900">Data NIK {{ $searchedNik }} tidak ditemukan</p>
                <p class="text-xs text-amber-700">Pastikan 16 digit NIK yang dimasukkan sudah benar atau silakan lakukan registrasi akun warga baru.</p>
            </div>
        @endif

        @if($warga)
            @php
                $badge = $warga->badge_status;
            @endphp
            <!-- KARTU DETAIL STATUS WARGA -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
                <!-- HEADER STATUS -->
                <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Ringkasan Identitas Warga</span>
                        <h3 class="text-lg font-bold text-slate-900 mt-0.5">{{ $warga->nama_lengkap }}</h3>
                        <p class="text-xs text-slate-500">{{ $warga->user?->email }} · {{ $warga->no_hp }}</p>
                    </div>

                    <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }} self-start sm:self-center">
                        <span class="w-2 h-2 rounded-full {{ $badge['dot'] }}"></span>
                        {{ $badge['label'] }}
                    </span>
                </div>

                <!-- BANNER PENJELASAN STATUS -->
                <div class="px-6 pt-5">
                    @if($warga->isVerified())
                        <div class="bg-emerald-50/90 border border-emerald-200 rounded-xl p-4 text-xs text-emerald-900 space-y-1">
                            <div class="font-bold">Akun Warga Terverifikasi Aktif</div>
                            <p class="text-emerald-800 leading-relaxed">
                                Selamat! Identitas KTP/KK dan domisili Anda di wilayah <span class="font-semibold">{{ $warga->wilayah?->nama }}</span> telah disetujui oleh pengelola TemJi. Anda berhak menggunakan layanan penjemputan sampah terjadwal dan setoran daur ulang berpoin.
                            </p>
                        </div>
                    @elseif($warga->isRejected())
                        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-xs text-rose-900 space-y-1.5">
                            <div class="font-bold">Pengajuan Verifikasi Ditolak — Perlu Perbaikan Dokumen</div>
                            <p class="text-rose-800 leading-relaxed">
                                Alasan Penolakan Admin: <span class="font-semibold">"{{ $warga->catatan_verifikasi ?? 'Dokumen identitas belum terbaca jelas.' }}"</span>
                            </p>
                        </div>
                    @else
                        <div class="bg-amber-50/90 border border-amber-200 rounded-xl p-4 text-xs text-amber-900 space-y-1">
                            <div class="font-bold">Menunggu Verifikasi Pengelola TemJi</div>
                            <p class="text-amber-800 leading-relaxed">
                                Dokumen identitas Anda telah diterima dan sedang berada dalam antrean pemeriksaan Admin wilayah <span class="font-semibold">{{ $warga->wilayah?->nama }}</span>.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- DETAIL INFORMASI KEPENDUDUKAN -->
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                        <div class="text-slate-400 text-[11px]">Nomor Induk Kependudukan (NIK)</div>
                        <div class="font-mono font-semibold text-slate-900 mt-0.5">{{ $warga->nik }}</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                        <div class="text-slate-400 text-[11px]">Nomor Kartu Keluarga (No. KK)</div>
                        <div class="font-mono font-semibold text-slate-900 mt-0.5">{{ $warga->no_kk }}</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                        <div class="text-slate-400 text-[11px]">Wilayah Layanan</div>
                        <div class="font-semibold text-slate-900 mt-0.5">{{ $warga->wilayah?->nama ?? '-' }}</div>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                        <div class="text-slate-400 text-[11px]">Waktu Pengajuan</div>
                        <div class="font-semibold text-slate-900 mt-0.5">
                            {{ $warga->created_at ? $warga->created_at->translatedFormat('d F Y, H:i') : '-' }}
                        </div>
                    </div>
                    <div class="sm:col-span-2 bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                        <div class="text-slate-400 text-[11px]">Alamat Lengkap Domisili</div>
                        <div class="font-medium text-slate-900 mt-0.5">{{ $warga->alamat }}</div>
                    </div>

                    @if($warga->catatan_verifikasi)
                        <div class="sm:col-span-2 bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                            <div class="text-slate-400 text-[11px]">Catatan Verifikator ({{ $warga->verifikator?->name ?? 'Admin TemJi' }})</div>
                            <div class="font-medium text-slate-800 mt-0.5">{{ $warga->catatan_verifikasi }}</div>
                        </div>
                    @endif
                </div>

                <!-- FORM PENGAJUAN ULANG JIKA DITOLAK -->
                @if($warga->isRejected())
                    <div class="px-6 pb-6 pt-2 border-t border-slate-100">
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 space-y-4">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">Unggah Ulang Bukti Identitas</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Silakan unggah foto KTP/KK yang lebih jelas dan perbarui alamat/nomor HP bila diperlukan.</p>
                            </div>

                            <form method="POST" action="{{ route('warga.registrasi.resubmit', $warga->id) }}" enctype="multipart/form-data" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="resubmit_foto" class="block text-xs font-medium text-slate-700 mb-1.5">
                                        Foto KTP / Kartu Keluarga Baru (JPG/PNG maks. 2 MB) <span class="text-rose-600">*</span>
                                    </label>
                                    <input
                                        type="file"
                                        name="foto_identitas"
                                        id="resubmit_foto"
                                        accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                        required
                                        class="block w-full text-xs text-slate-600 bg-white border border-slate-300 rounded-xl p-2 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#EBF7EE] file:text-[#14532D] cursor-pointer"
                                    >
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label for="resubmit_hp" class="block text-xs font-medium text-slate-700 mb-1">Perbarui Nomor HP (Opsional)</label>
                                        <input type="text" name="no_hp" id="resubmit_hp" value="{{ old('no_hp', $warga->no_hp) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:outline-none">
                                    </div>
                                    <div>
                                        <label for="resubmit_alamat" class="block text-xs font-medium text-slate-700 mb-1">Perbarui Alamat Domisili (Opsional)</label>
                                        <input type="text" name="alamat" id="resubmit_alamat" value="{{ old('alamat', $warga->alamat) }}" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:outline-none">
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="submit" class="px-5 py-2.5 bg-[#14532D] hover:bg-[#0f3d21] text-white rounded-xl font-semibold text-xs shadow-xs transition cursor-pointer">
                                        Kirim Ulang Dokumen Verifikasi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @endif

    </main>
</body>
</html>
