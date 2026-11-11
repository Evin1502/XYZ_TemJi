<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun Warga - TemJi</title>

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
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#14532D] flex items-center justify-center text-white shadow-xs">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/>
                        <path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/>
                    </svg>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 text-lg leading-tight tracking-tight">TemJi</h1>
                    <p class="text-xs text-slate-400 font-medium">Portal Layanan Warga</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                @if(\Illuminate\Support\Facades\Route::has('login'))
                    <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-xl bg-[#EBF7EE] hover:bg-emerald-100 text-[#14532D] font-semibold text-xs transition">
                        Masuk ke Akun
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- KOLOM KIRI: PANDUAN & ALUR VERIFIKASI (4 COLUMNS) -->
            <div class="lg:col-span-4 space-y-5">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                    <span class="inline-flex items-center gap-1.5 bg-[#EBF7EE] text-[#14532D] text-[11px] font-semibold px-3 py-1 rounded-full">
                        Pendaftaran Warga Resmi
                    </span>
                    <h2 class="text-xl font-bold text-slate-900 tracking-tight leading-snug">
                        Registrasi Akun Warga & Verifikasi Identitas
                    </h2>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Daftarkan akun keluarga Anda untuk mengakses layanan penjemputan sampah terjadwal, setoran daur ulang berpoin, dan transparansi iuran wilayah.
                    </p>

                    <div class="pt-3 border-t border-slate-100 space-y-3.5 text-xs">
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#14532D] text-white font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                            <div>
                                <div class="font-semibold text-slate-900">Isi Identitas Sesuai KTP & KK</div>
                                <p class="text-slate-500 mt-0.5 leading-relaxed">Pastikan 16 digit NIK dan Nomor KK sesuai dengan dokumen kependudukan aktif.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#14532D] text-white font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                            <div>
                                <div class="font-semibold text-slate-900">Unggah Foto Bukti Identitas</div>
                                <p class="text-slate-500 mt-0.5 leading-relaxed">Lampirkan foto KTP atau Kartu Keluarga yang jelas dan terbaca (JPG/PNG maks. 2 MB).</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-[#14532D] text-white font-bold text-[11px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                            <div>
                                <div class="font-semibold text-slate-900">Verifikasi Pengelola TemJi</div>
                                <p class="text-slate-500 mt-0.5 leading-relaxed">Admin memvalidasi kesesuaian domisili wilayah sebelum mengaktifkan status warga terverifikasi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-100/90 rounded-2xl p-4 text-xs text-slate-600 leading-relaxed">
                    <div class="font-semibold text-slate-800 mb-1">Perlindungan Data Warga</div>
                    Dokumen identitas hanya digunakan oleh pengelola resmi TemJi untuk validasi domisili layanan wilayah Sukapura & Sukabirus.
                </div>
            </div>

            <!-- KOLOM KANAN: FORMULIR REGISTRASI (8 COLUMNS) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
                <div class="pb-5 mb-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Formulir Pendaftaran Akun Warga</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Seluruh kolom bertanda bintang (<span class="text-rose-600">*</span>) wajib dilengkapi.</p>
                    </div>
                </div>

                @if($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3.5 rounded-xl text-xs mb-6">
                        <div class="font-semibold mb-1">Mohon periksa kembali isian formulir Anda:</div>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('warga.registrasi.store') }}" enctype="multipart/form-data" class="space-y-5" novalidate>
                    @csrf

                    <!-- SEKSI 1: DATA KEPENDUDUKAN -->
                    <div class="space-y-4">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">1. Data Kependudukan</div>

                        <div>
                            <label for="nama_lengkap" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Nama Lengkap Sesuai KTP <span class="text-rose-600">*</span>
                            </label>
                            <input
                                type="text"
                                name="nama_lengkap"
                                id="nama_lengkap"
                                value="{{ old('nama_lengkap') }}"
                                required
                                maxlength="100"
                                placeholder="Contoh: Budi Santoso"
                                class="w-full px-3.5 py-2.5 border {{ $errors->has('nama_lengkap') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                            >
                            @error('nama_lengkap')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="nik" class="block text-xs font-medium text-slate-700">
                                        NIK (16 Digit) <span class="text-rose-600">*</span>
                                    </label>
                                    <span id="nik-counter" class="text-[11px] text-slate-400 font-mono">0/16</span>
                                </div>
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    name="nik"
                                    id="nik"
                                    value="{{ old('nik') }}"
                                    required
                                    maxlength="16"
                                    placeholder="320412xxxxxxxxxx"
                                    class="w-full px-3.5 py-2.5 border {{ $errors->has('nik') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} rounded-xl text-xs font-mono text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                                >
                                @error('nik')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label for="no_kk" class="block text-xs font-medium text-slate-700">
                                        Nomor Kartu Keluarga (16 Digit) <span class="text-rose-600">*</span>
                                    </label>
                                    <span id="kk-counter" class="text-[11px] text-slate-400 font-mono">0/16</span>
                                </div>
                                <input
                                    type="text"
                                    inputmode="numeric"
                                    name="no_kk"
                                    id="no_kk"
                                    value="{{ old('no_kk') }}"
                                    required
                                    maxlength="16"
                                    placeholder="320412xxxxxxxxxx"
                                    class="w-full px-3.5 py-2.5 border {{ $errors->has('no_kk') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} rounded-xl text-xs font-mono text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                                >
                                @error('no_kk')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI 2: KONTAK & DOMISILI WILAYAH -->
                    <div class="pt-4 border-t border-slate-100 space-y-4">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">2. Kontak & Domisili Wilayah</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="email" class="block text-xs font-medium text-slate-700 mb-1.5">
                                    Alamat Email Aktif <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="nama@email.com"
                                    class="w-full px-3.5 py-2.5 border {{ $errors->has('email') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                                >
                                @error('email')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="no_hp" class="block text-xs font-medium text-slate-700 mb-1.5">
                                    Nomor HP / WhatsApp <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    inputmode="tel"
                                    name="no_hp"
                                    id="no_hp"
                                    value="{{ old('no_hp') }}"
                                    required
                                    maxlength="15"
                                    placeholder="0812xxxxxxxx"
                                    class="w-full px-3.5 py-2.5 border {{ $errors->has('no_hp') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                                >
                                @error('no_hp')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="wilayah_id" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Wilayah Layanan Domisili <span class="text-rose-600">*</span>
                            </label>
                            <select
                                name="wilayah_id"
                                id="wilayah_id"
                                required
                                class="w-full px-3.5 py-2.5 border {{ $errors->has('wilayah_id') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} rounded-xl text-xs text-slate-900 bg-white focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                            >
                                <option value="">-- Pilih Wilayah Layanan (Sukapura / Sukabirus) --</option>
                                @foreach($wilayahList as $wilayah)
                                    <option value="{{ $wilayah->id }}" {{ (string) old('wilayah_id') === (string) $wilayah->id ? 'selected' : '' }}>
                                        {{ $wilayah->nama }} ({{ $wilayah->kode_wilayah }})
                                    </option>
                                @endforeach
                            </select>
                            @error('wilayah_id')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="alamat" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Alamat Lengkap Domisili (Jalan, Gang, RT/RW, No. Rumah) <span class="text-rose-600">*</span>
                            </label>
                            <textarea
                                name="alamat"
                                id="alamat"
                                rows="2"
                                required
                                maxlength="500"
                                placeholder="Contoh: Jl. Sukapura No. 14, RT 02 / RW 05, Desa Sukapura"
                                class="w-full px-3.5 py-2.5 border {{ $errors->has('alamat') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                            >{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- SEKSI 3: UNGGAH BUKTI IDENTITAS -->
                    <div class="pt-4 border-t border-slate-100 space-y-4">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">3. Bukti Dokumen Identitas</div>

                        <div>
                            <label for="foto_identitas" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Unggah Foto / Scan KTP atau Kartu Keluarga <span class="text-rose-600">*</span>
                            </label>
                            <div class="border-2 border-dashed {{ $errors->has('foto_identitas') ? 'border-rose-300 bg-rose-50/30' : 'border-slate-300 bg-slate-50/60' }} rounded-2xl p-4 text-center hover:border-[#14532D] transition">
                                <input
                                    type="file"
                                    name="foto_identitas"
                                    id="foto_identitas"
                                    accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                                    required
                                    class="block w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#EBF7EE] file:text-[#14532D] hover:file:bg-emerald-100 cursor-pointer"
                                >
                                <p class="text-[11px] text-slate-400 mt-2">
                                    Format yang didukung: JPG, JPEG, atau PNG (Maksimal 2 MB). Pastikan NIK dan nama terlihat jelas.
                                </p>

                                <div id="preview-wrapper" class="hidden mt-3 pt-3 border-t border-slate-200/70">
                                    <p class="text-[11px] font-medium text-slate-600 mb-2">Pratinjau Dokumen:</p>
                                    <img id="preview-image" src="#" alt="Pratinjau KTP/KK" class="max-h-44 mx-auto rounded-xl border border-slate-200 shadow-xs object-contain">
                                </div>
                            </div>
                            @error('foto_identitas')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- SEKSI 4: KEAMANAN AKUN -->
                    <div class="pt-4 border-t border-slate-100 space-y-4">
                        <div class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">4. Kata Sandi Akun</div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-xs font-medium text-slate-700 mb-1.5">
                                    Kata Sandi <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    required
                                    minlength="8"
                                    placeholder="Minimal 8 karakter"
                                    class="w-full px-3.5 py-2.5 border {{ $errors->has('password') ? 'border-rose-400 bg-rose-50/30' : 'border-slate-300' }} rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                                >
                                @error('password')
                                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-medium text-slate-700 mb-1.5">
                                    Konfirmasi Kata Sandi <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    required
                                    minlength="8"
                                    placeholder="Ulangi kata sandi"
                                    class="w-full px-3.5 py-2.5 border border-slate-300 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-[11px] text-slate-500">
                            Sudah pernah mendaftar? <a href="{{ route('warga.registrasi.status') }}" class="text-[#14532D] font-semibold hover:underline">Cek status verifikasi di sini</a>.
                        </p>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 bg-[#14532D] hover:bg-[#0f3d21] text-white px-6 py-2.5 rounded-xl font-semibold text-xs shadow-xs transition cursor-pointer">
                            <span>Daftar & Ajukan Verifikasi</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function bindDigitCounter(inputId, counterId) {
            const input = document.getElementById(inputId);
            const counter = document.getElementById(counterId);
            if (!input || !counter) return;

            const update = () => {
                input.value = input.value.replace(/\D/g, '').slice(0, 16);
                counter.textContent = `${input.value.length}/16`;
                counter.className = input.value.length === 16
                    ? 'text-[11px] text-emerald-700 font-semibold font-mono'
                    : 'text-[11px] text-slate-400 font-mono';
            };
            input.addEventListener('input', update);
            update();
        }

        bindDigitCounter('nik', 'nik-counter');
        bindDigitCounter('no_kk', 'kk-counter');

        const fotoInput = document.getElementById('foto_identitas');
        const previewWrapper = document.getElementById('preview-wrapper');
        const previewImage = document.getElementById('preview-image');

        if (fotoInput && previewWrapper && previewImage) {
            fotoInput.addEventListener('change', function () {
                const file = this.files && this.files[0];
                if (!file || !file.type.startsWith('image/')) {
                    previewWrapper.classList.add('hidden');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewWrapper.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
</body>
</html>
