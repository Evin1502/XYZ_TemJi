@extends('layouts.admin')

@section('title', 'Aturan Penukaran Poin')
@section('breadcrumb_parent', 'Kategori & Poin')
@section('breadcrumb_current', 'Aturan penukaran')

@section('content')
<div class="space-y-6">

    <!-- HEADER & SUBTITLE -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Aturan penukaran poin</h2>
            <p class="text-xs text-slate-500 mt-1">
                Poin bisa ditukar jadi potongan iuran. Poin adalah reward, bukan pengganti iuran.
            </p>
        </div>
    </div>

    <!-- TABS -->
    <div class="flex items-center gap-2 border-b border-slate-200/60 pb-3">
        <a href="{{ route('admin.kategori.index') }}" class="px-4 py-1.5 rounded-full text-xs font-medium text-slate-500 hover:text-slate-800 transition">
            Kategori
        </a>
        <span class="bg-white px-4 py-1.5 rounded-full text-xs font-semibold text-slate-900 shadow-xs border border-slate-200/80 cursor-default">
            Aturan penukaran
        </span>
    </div>

    <!-- TWO-COLUMN LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- KOLOM KIRI: ATURAN AKTIF & RIWAYAT ATURAN (7 COLUMNS) -->
        <div class="lg:col-span-7 space-y-6">

            <!-- CARD ATURAN AKTIF -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                <!-- HEADER CARD -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Aturan aktif</h3>
                        <p class="text-xs text-slate-400 font-normal">Perubahan berlaku untuk tagihan bulan berikutnya</p>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 bg-[#EBF7EE] text-[#14532D] text-[11px] font-semibold px-3 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#15803D]"></span>
                            Berlaku sejak {{ $aturanAktif->berlaku_mulai ? $aturanAktif->berlaku_mulai->translatedFormat('j M Y') : '1 Sep 2026' }}
                        </span>
                    </div>
                </div>

                <!-- FORM ATURAN PENUKARAN -->
                <form id="form-aturan" method="POST" action="{{ route('admin.aturan-penukaran.store') }}" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- INPUT 1: NILAI 1 POIN -->
                        <div>
                            <label for="input-nilai-poin" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Nilai 1 poin
                            </label>
                            <div class="flex rounded-xl border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-[#14532D] focus-within:border-transparent transition">
                                <span class="bg-slate-50 border-r border-slate-200 px-3 py-2 text-xs text-slate-500 flex items-center font-medium">
                                    Rp
                                </span>
                                <input
                                    type="number"
                                    name="nilai_rupiah_per_poin"
                                    id="input-nilai-poin"
                                    value="{{ old('nilai_rupiah_per_poin', $aturanAktif->nilai_rupiah_per_poin) }}"
                                    min="1"
                                    required
                                    class="flex-1 px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none"
                                    placeholder="100"
                                >
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Contoh: 50 poin = <span id="hint-50-poin" class="font-medium text-slate-600">Rp {{ number_format($aturanAktif->nilai_rupiah_per_poin * 50, 0, ',', '.') }}</span>
                            </p>
                            @error('nilai_rupiah_per_poin')
                                <p class="text-[11px] text-rose-600 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- INPUT 2: BATAS MAKSIMAL POTONGAN -->
                        <div>
                            <label for="input-batas-persen" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Batas maksimal potongan
                            </label>
                            <div class="flex rounded-xl border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-[#14532D] focus-within:border-transparent transition">
                                <input
                                    type="number"
                                    name="batas_maksimal_potongan_persen"
                                    id="input-batas-persen"
                                    value="{{ old('batas_maksimal_potongan_persen', $aturanAktif->batas_maksimal_potongan_persen) }}"
                                    min="0"
                                    max="100"
                                    required
                                    class="flex-1 px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none"
                                    placeholder="50"
                                >
                                <span class="bg-slate-50 border-l border-slate-200 px-3 py-2 text-xs text-slate-500 flex items-center font-medium">
                                    % dari tagihan
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Warga tetap membayar minimal sisa tagihan
                            </p>
                            @error('batas_maksimal_potongan_persen')
                                <p class="text-[11px] text-rose-600 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- INPUT 3: MINIMAL POIN UNTUK DITUKAR -->
                        <div>
                            <label for="input-min-poin" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Minimal poin untuk ditukar
                            </label>
                            <div class="flex rounded-xl border border-slate-300 overflow-hidden focus-within:ring-2 focus-within:ring-[#14532D] focus-within:border-transparent transition">
                                <input
                                    type="number"
                                    name="minimal_poin_tukar"
                                    id="input-min-poin"
                                    value="{{ old('minimal_poin_tukar', $aturanAktif->minimal_poin_tukar) }}"
                                    min="0"
                                    required
                                    class="flex-1 px-3 py-2 text-xs font-semibold text-slate-900 focus:outline-none"
                                    placeholder="50"
                                >
                                <span class="bg-slate-50 border-l border-slate-200 px-3 py-2 text-xs text-slate-500 flex items-center font-medium">
                                    poin
                                </span>
                            </div>
                            @error('minimal_poin_tukar')
                                <p class="text-[11px] text-rose-600 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- INPUT 4: BERLAKU MULAI -->
                        <div>
                            <label for="input-berlaku-mulai" class="block text-xs font-medium text-slate-700 mb-1.5">
                                Berlaku mulai
                            </label>
                            <input
                                type="text"
                                name="berlaku_mulai"
                                id="input-berlaku-mulai"
                                value="{{ old('berlaku_mulai', 'Tagihan Desember 2026') }}"
                                required
                                class="w-full px-3 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:ring-2 focus:ring-[#14532D] focus:border-transparent focus:outline-none transition"
                                placeholder="Contoh: Tagihan Desember 2026 atau YYYY-MM-DD"
                            >
                            @error('berlaku_mulai')
                                <p class="text-[11px] text-rose-600 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-slate-100">
                        <div class="text-[11px] text-slate-400">
                            Terakhir diubah {{ $aturanAktif->updated_at ? $aturanAktif->updated_at->translatedFormat('j M Y') : '28 Agu 2026' }} oleh {{ $aturanAktif->diubahOleh?->name ?? 'Admin TemJi' }}
                        </div>
                        <div class="flex items-center gap-2.5 self-end sm:self-auto">
                            <button
                                type="reset"
                                id="btn-reset"
                                class="px-4 py-2 border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-xl text-xs font-medium transition cursor-pointer"
                            >
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="px-5 py-2 bg-[#14532D] hover:bg-[#0f3d21] text-white rounded-xl text-xs font-semibold shadow-xs transition cursor-pointer"
                            >
                                Simpan aturan
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- CARD RIWAYAT ATURAN -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-4">
                <div class="pb-2 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Riwayat aturan</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[11px] font-semibold text-slate-400 border-b border-slate-100">
                                <th class="pb-3 font-medium">Periode berlaku</th>
                                <th class="pb-3 font-medium">Nilai 1 poin</th>
                                <th class="pb-3 font-medium">Maks. potongan</th>
                                <th class="pb-3 font-medium">Minimal tukar</th>
                                <th class="pb-3 font-medium text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($riwayatAturan as $histori)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 pr-3 font-medium text-slate-800">
                                    {{ $histori->periode_label }}
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-slate-900">
                                    Rp {{ number_format($histori->nilai_rupiah_per_poin, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-3 text-slate-700">
                                    {{ $histori->batas_maksimal_potongan_persen }}%
                                </td>
                                <td class="py-3.5 px-3 text-slate-700">
                                    {{ $histori->minimal_poin_tukar }} poin
                                </td>
                                <td class="py-3.5 pl-3 text-center">
                                    @php
                                        $badge = $histori->badge_status;
                                    @endphp
                                    @if($histori->status_label === 'Berlaku')
                                        <span class="inline-block bg-[#DCFCE7] text-[#15803D] text-[10px] font-semibold px-2.5 py-0.5 rounded-full">
                                            Berlaku
                                        </span>
                                    @elseif($histori->status_label === 'Terjadwal')
                                        <span class="inline-block bg-[#FEF3C7] text-[#D97706] text-[10px] font-semibold px-2.5 py-0.5 rounded-full">
                                            Terjadwal
                                        </span>
                                    @else
                                        <span class="inline-block bg-slate-100 text-slate-500 text-[10px] font-semibold px-2.5 py-0.5 rounded-full">
                                            Selesai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-xs text-slate-400">
                                    Belum ada rekam riwayat aturan penukaran.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: SIMULASI UNTUK WARGA (5 COLUMNS) -->
        <div class="lg:col-span-5 space-y-6">

            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                <!-- HEADER SIMULASI -->
                <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Simulasi untuk warga</h3>
                        <p class="text-xs text-slate-400 font-normal">Tagihan Sukapura, saldo 180 poin</p>
                    </div>
                </div>

                <!-- RINCIAN SIMULASI -->
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Tagihan iuran</span>
                        <span class="font-semibold text-slate-900">Rp {{ number_format($tagihanContoh, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Nilai saldo 180 poin</span>
                        <span id="sim-nilai-saldo" class="font-semibold text-slate-900">
                            Rp {{ number_format($simulasi['nilai_saldo_rupiah'], 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span id="sim-label-batas" class="text-slate-500">
                            Batas potongan {{ $aturanAktif->batas_maksimal_potongan_persen }}%
                        </span>
                        <span id="sim-batas-rupiah" class="font-semibold text-slate-900">
                            Rp {{ number_format($simulasi['batas_potongan_rupiah'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- VISUAL SPLIT PROGRESS BAR -->
                <div class="space-y-1.5 pt-2">
                    <div class="h-3 rounded-full bg-slate-200 overflow-hidden flex shadow-2xs">
                        <div
                            id="sim-bar-potongan"
                            style="width: {{ $simulasi['persen_potongan'] }}%;"
                            class="bg-[#14532D] h-full transition-all duration-300"
                        ></div>
                        <div
                            id="sim-bar-dibayar"
                            style="width: {{ $simulasi['persen_dibayar'] }}%;"
                            class="bg-slate-300 h-full transition-all duration-300"
                        ></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-slate-400 font-medium px-0.5">
                        <span class="text-[#14532D] font-semibold">Potongan poin</span>
                        <span>Dibayar warga</span>
                    </div>
                </div>

                <!-- HASIL REKAP POTONGAN & SISA BAYAR -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-slate-600 font-medium">Potongan dipakai</span>
                        <span id="sim-potongan-dipakai" class="text-xs font-semibold text-rose-600">
                            - Rp {{ number_format($simulasi['potongan_dipakai'], 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="flex items-baseline justify-between">
                        <span class="text-sm font-bold text-slate-900">Sisa yang dibayar</span>
                        <span id="sim-sisa-bayar" class="text-xl font-bold text-slate-900">
                            Rp {{ number_format($simulasi['sisa_yang_dibayar'], 0, ',', '.') }}
                        </span>
                    </div>

                    <div id="sim-keterangan-poin" class="text-[11px] text-slate-400 text-right">
                        Poin terpakai {{ $simulasi['poin_terpakai'] }} · sisa saldo {{ $simulasi['sisa_saldo_poin'] }} poin
                    </div>
                </div>

                <!-- KOTAK CATATAN AMBER: SALDO MELEBIHI BATAS TIDAK HANGUS -->
                <div class="bg-[#FFFBEB] border border-[#FDE68A] rounded-xl p-3.5 flex items-start gap-2.5 text-xs text-[#92400E] leading-relaxed">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>
                        Saldo melebihi batas tidak hangus. Sisanya dipakai di tagihan berikutnya.
                    </p>
                </div>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    (function () {
        const nominalTagihan = {{ $tagihanContoh }};
        const saldoPoin = {{ $saldoPoinContoh }};

        const inputNilaiPoin = document.getElementById('input-nilai-poin');
        const inputBatasPersen = document.getElementById('input-batas-persen');
        const inputMinPoin = document.getElementById('input-min-poin');
        const btnReset = document.getElementById('btn-reset');

        const hint50Poin = document.getElementById('hint-50-poin');
        const simNilaiSaldo = document.getElementById('sim-nilai-saldo');
        const simLabelBatas = document.getElementById('sim-label-batas');
        const simBatasRupiah = document.getElementById('sim-batas-rupiah');
        const simBarPotongan = document.getElementById('sim-bar-potongan');
        const simBarDibayar = document.getElementById('sim-bar-dibayar');
        const simPotonganDipakai = document.getElementById('sim-potongan-dipakai');
        const simSisaBayar = document.getElementById('sim-sisa-bayar');
        const simKeteranganPoin = document.getElementById('sim-keterangan-poin');

        function formatRupiah(num) {
            return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        function hitungLiveSimulasi() {
            const nilaiPerPoin = Math.max(1, parseInt(inputNilaiPoin?.value || 0, 10));
            const batasPersen = Math.min(100, Math.max(0, parseInt(inputBatasPersen?.value || 0, 10)));
            const minPoin = Math.max(0, parseInt(inputMinPoin?.value || 0, 10));

            // Perbarui hint "Contoh: 50 poin = Rp X"
            if (hint50Poin) {
                hint50Poin.textContent = formatRupiah(50 * nilaiPerPoin);
            }

            // Perhitungan simulasi warga
            const nilaiSaldoRupiah = saldoPoin * nilaiPerPoin;
            const batasRupiah = Math.round((nominalTagihan * batasPersen) / 100);
            const cukupMinimal = saldoPoin >= minPoin;

            let potonganDipakai = 0;
            let poinTerpakai = 0;

            if (cukupMinimal && nominalTagihan > 0 && saldoPoin > 0) {
                const potonganMaksimal = Math.min(nilaiSaldoRupiah, batasRupiah, nominalTagihan);
                poinTerpakai = Math.min(saldoPoin, Math.ceil(potonganMaksimal / nilaiPerPoin));
                potonganDipakai = poinTerpakai * nilaiPerPoin;

                if (potonganDipakai > batasRupiah) {
                    poinTerpakai = Math.floor(batasRupiah / nilaiPerPoin);
                    potonganDipakai = poinTerpakai * nilaiPerPoin;
                }
            }

            const sisaSaldoPoin = Math.max(0, saldoPoin - poinTerpakai);
            const sisaYangDibayar = Math.max(0, nominalTagihan - potonganDipakai);

            const persenPotongan = nominalTagihan > 0 ? Math.min(100, Math.round((potonganDipakai / nominalTagihan) * 100)) : 0;
            const persenDibayar = 100 - persenPotongan;

            // Update DOM element
            if (simNilaiSaldo) simNilaiSaldo.textContent = formatRupiah(nilaiSaldoRupiah);
            if (simLabelBatas) simLabelBatas.textContent = `Batas potongan ${batasPersen}%`;
            if (simBatasRupiah) simBatasRupiah.textContent = formatRupiah(batasRupiah);

            if (simBarPotongan) simBarPotongan.style.width = persenPotongan + '%';
            if (simBarDibayar) simBarDibayar.style.width = persenDibayar + '%';

            if (simPotonganDipakai) simPotonganDipakai.textContent = `- ${formatRupiah(potonganDipakai)}`;
            if (simSisaBayar) simSisaBayar.textContent = formatRupiah(sisaYangDibayar);
            if (simKeteranganPoin) {
                simKeteranganPoin.textContent = `Poin terpakai ${poinTerpakai} · sisa saldo ${sisaSaldoPoin} poin`;
            }
        }

        [inputNilaiPoin, inputBatasPersen, inputMinPoin].forEach(el => {
            if (el) {
                el.addEventListener('input', hitungLiveSimulasi);
                el.addEventListener('change', hitungLiveSimulasi);
            }
        });

        if (btnReset) {
            btnReset.addEventListener('click', () => {
                setTimeout(hitungLiveSimulasi, 50);
            });
        }
    })();
</script>
@endpush
@endsection
