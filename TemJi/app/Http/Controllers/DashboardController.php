<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar: Agregasi data per tanggal dari tb_pengajuan_sampah dan detail_penimbangan
        // Asumsi: status 'Selesai' menandakan penjemputan berhasil
        $trendData = DB::table('pengajuan_penjemputan')
            ->join('detail_penimbangan', 'pengajuan_penjemputan.id_pengajuan', '=', 'detail_penimbangan.id_pengajuan')
            ->select(
                DB::raw('DATE(pengajuan_penjemputan.waktu_selesai) as tanggal'),
                DB::raw('COUNT(DISTINCT pengajuan_penjemputan.id_pengajuan) as jumlah_penjemputan'),
                DB::raw('SUM(detail_penimbangan.berat_kg) as total_kg_daur_ulang')
            )
            ->where('pengajuan_penjemputan.status', 'Selesai')
            ->groupBy(DB::raw('DATE(pengajuan_penjemputan.waktu_selesai)'))
            ->orderBy('tanggal', 'asc')
            ->get();

        // Kirim data ke view (nanti view dibuat di Hari 2)
        return view('dashboard.index', compact('trendData'));
    }
}