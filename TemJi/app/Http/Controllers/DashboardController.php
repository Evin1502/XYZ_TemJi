<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Hari 2: Query dasar per hari (belum ada filter)
        $trendData = DB::table('pengajuan_penjemputan')
            ->join('detail_penimbangan', 'pengajuan_penjemputan.id_pengajuan', '=', 'detail_penimbangan.id_pengajuan')
            ->select(
                DB::raw('DATE(pengajuan_penjemputan.waktu_selesai) as tanggal'),
                DB::raw('COUNT(DISTINCT pengajuan_penjemputan.id_pengajuan) as jumlah_penjemputan'),
                DB::raw('SUM(detail_penimbangan.berat_kg) as total_kg_daur_ulang')
            )
            ->where('pengajuan_penjemputan.status', 'Selesai')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('dashboard.index', compact('trendData'));
    }
}