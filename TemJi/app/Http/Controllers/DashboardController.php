<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Tangkap parameter filter dari UI Dashboard
        $wilayah = $request->input('wilayah');
        $kategori = $request->input('kategori');
        $periode = $request->input('periode', 'harian');

        // Query dengan join ke tabel pengguna untuk filter wilayah
        $query = DB::table('pengajuan_penjemputan')
            ->join('detail_penimbangan', 'pengajuan_penjemputan.id_pengajuan', '=', 'detail_penimbangan.id_pengajuan')
            ->join('pengguna', 'pengajuan_penjemputan.id_warga', '=', 'pengguna.id_pengguna')
            ->where('pengajuan_penjemputan.status', 'Selesai');

        // Filter Berdasarkan Wilayah
        if (!empty($wilayah)) {
            $query->where('pengguna.id_wilayah', $wilayah);
        }

        // Filter Berdasarkan Kategori Sampah
        if (!empty($kategori)) {
            $query->where('detail_penimbangan.id_kategori', $kategori);
        }

        // Format grouping berdasarkan periode (harian, mingguan, bulanan)
        $format = '%Y-%m-%d'; 
        if ($periode == 'mingguan') {
            $format = '%x-W%v';
        } elseif ($periode == 'bulanan') {
            $format = '%Y-%m'; 
        }

        $trendData = $query->select(
                DB::raw("DATE_FORMAT(pengajuan_penjemputan.waktu_selesai, '$format') as tanggal"),
                DB::raw('COUNT(DISTINCT pengajuan_penjemputan.id_pengajuan) as jumlah_penjemputan'),
                DB::raw('SUM(detail_penimbangan.berat_kg) as total_kg_daur_ulang')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('dashboard.index', compact('trendData', 'wilayah', 'kategori', 'periode'));
    }

    // Method Ekspor Laporan PBI 17
    public function export(Request $request)
    {
        $wilayah  = $request->input('wilayah');
        $kategori = $request->input('kategori');
        $periode  = $request->input('periode', 'harian');
        $format   = $request->input('format', 'csv');

        // Query data terfilter dari database
        $query = DB::table('pengajuan_penjemputan')
            ->join('detail_penimbangan', 'pengajuan_penjemputan.id_pengajuan', '=', 'detail_penimbangan.id_pengajuan')
            ->join('pengguna', 'pengajuan_penjemputan.id_warga', '=', 'pengguna.id_pengguna')
            ->where('pengajuan_penjemputan.status', 'Selesai');

        if (!empty($wilayah)) {
            $query->where('pengguna.id_wilayah', $wilayah);
        }

        if (!empty($kategori)) {
            $query->where('detail_penimbangan.id_kategori', $kategori);
        }

        $dateFormat = '%Y-%m-%d';
        if ($periode == 'mingguan') {
            $dateFormat = '%x-W%v';
        } elseif ($periode == 'bulanan') {
            $dateFormat = '%Y-%m';
        }

        $data = $query->select(
                DB::raw("DATE_FORMAT(pengajuan_penjemputan.waktu_selesai, '$dateFormat') as periode_waktu"),
                DB::raw('COUNT(DISTINCT pengajuan_penjemputan.id_pengajuan) as jumlah_penjemputan'),
                DB::raw('SUM(detail_penimbangan.berat_kg) as total_kg')
            )
            ->groupBy('periode_waktu')
            ->orderBy('periode_waktu', 'asc')
            ->get();

        // Generasi CSV
        if ($format === 'csv') {
            $fileName = 'temji-laporan-' . $periode . '-' . date('Y-m-d') . '.csv';

            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                // UTF-8 BOM agar rapi di Microsoft Excel
                fputs($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

                // Header Kolom CSV
                fputcsv($file, ['Periode', 'Jumlah Penjemputan', 'Total Berat Daur Ulang (Kg)']);

                // Baris Data
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->periode_waktu,
                        $row->jumlah_penjemputan,
                        number_format($row->total_kg, 2, '.', '')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Format PDF dalam pengembangan');
    }
}
