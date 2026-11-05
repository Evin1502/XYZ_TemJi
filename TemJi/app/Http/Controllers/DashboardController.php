<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Tangkap parameter filter dari UI Dashboard (PBI 16 Final)
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
}