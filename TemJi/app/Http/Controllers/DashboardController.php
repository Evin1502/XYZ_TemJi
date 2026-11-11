<?php

namespace App\Http\Controllers;

use App\Models\KategoriDaurUlang;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard analitik tren daur ulang (PBI 16).
     */
    public function index(Request $request)
    {
        $wilayah  = $request->input('wilayah');
        $kategori = $request->input('kategori');
        $periode  = $request->input('periode', 'harian');

        $query = DB::table('pengajuan_daur_ulang')
            ->leftJoin('pengajuan_daur_ulang_items', 'pengajuan_daur_ulang.id', '=', 'pengajuan_daur_ulang_items.pengajuan_id')
            ->whereIn('pengajuan_daur_ulang.status', ['selesai', 'disetujui']);

        // Filter Berdasarkan Wilayah
        if (!empty($wilayah)) {
            $query->where('pengajuan_daur_ulang.wilayah_id', $wilayah);
        }

        // Filter Berdasarkan Kategori Sampah
        if (!empty($kategori)) {
            $query->where('pengajuan_daur_ulang_items.kategori_id', $kategori);
        }

        $dateExpr = $this->getDateGroupExpression($periode);

        $trendData = $query->select(
                DB::raw("{$dateExpr} as tanggal"),
                DB::raw('COUNT(DISTINCT pengajuan_daur_ulang.id) as jumlah_penjemputan'),
                DB::raw('COALESCE(SUM(pengajuan_daur_ulang_items.estimasi_berat_kg), 0) as total_kg_daur_ulang')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        $wilayahList  = Wilayah::where('is_aktif', true)->orderBy('nama')->get();
        $kategoriList = KategoriDaurUlang::orderBy('nama')->get();

        return view('dashboard.index', compact('trendData', 'wilayah', 'kategori', 'periode', 'wilayahList', 'kategoriList'));
    }

    /**
     * Ekspor data laporan tren ke CSV (PBI 17).
     */
    public function export(Request $request)
    {
        $wilayah  = $request->input('wilayah');
        $kategori = $request->input('kategori');
        $periode  = $request->input('periode', 'harian');
        $format   = $request->input('format', 'csv');

        $query = DB::table('pengajuan_daur_ulang')
            ->leftJoin('pengajuan_daur_ulang_items', 'pengajuan_daur_ulang.id', '=', 'pengajuan_daur_ulang_items.pengajuan_id')
            ->whereIn('pengajuan_daur_ulang.status', ['selesai', 'disetujui']);

        if (!empty($wilayah)) {
            $query->where('pengajuan_daur_ulang.wilayah_id', $wilayah);
        }

        if (!empty($kategori)) {
            $query->where('pengajuan_daur_ulang_items.kategori_id', $kategori);
        }

        $dateExpr = $this->getDateGroupExpression($periode);

        $data = $query->select(
                DB::raw("{$dateExpr} as periode_waktu"),
                DB::raw('COUNT(DISTINCT pengajuan_daur_ulang.id) as jumlah_penjemputan'),
                DB::raw('COALESCE(SUM(pengajuan_daur_ulang_items.estimasi_berat_kg), 0) as total_kg')
            )
            ->groupBy('periode_waktu')
            ->orderBy('periode_waktu', 'asc')
            ->get();

        // Generasi CSV
        if ($format === 'csv') {
            $fileName = 'temji-laporan-' . $periode . '-' . date('Y-m-d') . '.csv';

            $headers = [
                'Content-Type'        => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma'              => 'no-cache',
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
                'Expires'             => '0',
            ];

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM agar rapi di Microsoft Excel
                fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Header Kolom CSV
                fputcsv($file, ['Periode', 'Jumlah Penjemputan', 'Total Berat Daur Ulang (Kg)']);

                // Baris Data
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->periode_waktu ?? '-',
                        $row->jumlah_penjemputan,
                        number_format((float) $row->total_kg, 2, '.', ''),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Format PDF dalam pengembangan');
    }

    /**
     * Generator ekspresi SQL format tanggal yang kompatibel dengan SQLite dan MySQL.
     */
    private function getDateGroupExpression(string $periode): string
    {
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        if ($periode === 'mingguan') {
            return $isSqlite
                ? "strftime('%Y-W%W', pengajuan_daur_ulang.created_at)"
                : "DATE_FORMAT(pengajuan_daur_ulang.created_at, '%x-W%v')";
        }

        if ($periode === 'bulanan') {
            return $isSqlite
                ? "strftime('%Y-%m', pengajuan_daur_ulang.created_at)"
                : "DATE_FORMAT(pengajuan_daur_ulang.created_at, '%Y-%m')";
        }

        // Harian default
        return $isSqlite
            ? "strftime('%Y-%m-%d', pengajuan_daur_ulang.created_at)"
            : "DATE_FORMAT(pengajuan_daur_ulang.created_at, '%Y-%m-%d')";
    }
}
