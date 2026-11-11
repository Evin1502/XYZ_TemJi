<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJadwalPenjemputanRequest;
use App\Models\JadwalPenjemputan;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JadwalPenjemputanController extends Controller
{
    private const ZONA_WAKTU = 'Asia/Jakarta';

    /**
     * Tampilkan kalender jadwal rutin mingguan beserta form tambah jadwal.
     */
    public function index(Request $request): View
    {
        $senin = $this->awalMinggu($request->query('minggu'));
        $sabtu = $senin->copy()->addDays(5);

        $hariMinggu = collect(JadwalPenjemputan::HARI)->map(fn (string $label, int $hari) => [
            'hari' => $hari,
            'label' => $label,
            'tanggal' => $senin->copy()->addDays($hari - 1),
        ]);

        $wilayahList = Wilayah::where('is_aktif', true)->orderBy('id')->get();
        $wilayahId = $request->integer('wilayah') ?: null;

        $jadwalList = JadwalPenjemputan::with(['wilayah', 'petugas'])
            ->untukWilayah($wilayahId)
            ->orderBy('jam_mulai')
            ->get();
        $jadwalPerHari = $jadwalList->groupBy('hari');

        $petugasList = User::petugas()->orderBy('name')->get();

        return view('admin.jadwal.index', [
            'senin' => $senin,
            'labelMinggu' => $this->labelMinggu($senin, $sabtu),
            'mingguSebelumnya' => $senin->copy()->subWeek()->toDateString(),
            'mingguBerikutnya' => $senin->copy()->addWeek()->toDateString(),
            'hariMinggu' => $hariMinggu,
            'wilayahList' => $wilayahList,
            'wilayahId' => $wilayahId,
            'jadwalList' => $jadwalList,
            'jadwalPerHari' => $jadwalPerHari,
            'petugasList' => $petugasList,
        ]);
    }

    /**
     * Simpan jadwal rutin baru (berulang tiap minggu).
     */
    public function store(StoreJadwalPenjemputanRequest $request): RedirectResponse
    {
        JadwalPenjemputan::create($request->validated());

        return redirect()
            ->route('admin.jadwal.index', array_filter([
                'minggu' => $request->input('minggu'),
                'wilayah' => $request->input('filter_wilayah'),
            ]))
            ->with('success', 'Jadwal berhasil disimpan.');
    }

    /**
     * Senin dari minggu yang diminta (format Y-m-d), atau minggu ini bila kosong/tidak valid.
     */
    private function awalMinggu(?string $minggu): Carbon
    {
        $tanggal = rescue(
            fn () => $minggu ? Carbon::createFromFormat('Y-m-d', $minggu, self::ZONA_WAKTU) : null,
            report: false
        ) ?? Carbon::now(self::ZONA_WAKTU);

        return $tanggal->startOfWeek(Carbon::MONDAY)->locale('id');
    }

    /**
     * Label rentang minggu, mis. "2 – 7 November 2026" atau "30 November – 5 Desember 2026".
     */
    private function labelMinggu(Carbon $awal, Carbon $akhir): string
    {
        $formatAwal = match (true) {
            $awal->year !== $akhir->year => 'j F Y',
            $awal->month !== $akhir->month => 'j F',
            default => 'j',
        };

        return $awal->translatedFormat($formatAwal) . ' – ' . $akhir->translatedFormat('j F Y');
    }
}
