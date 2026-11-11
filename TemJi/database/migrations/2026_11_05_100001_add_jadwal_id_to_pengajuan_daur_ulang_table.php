<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menghubungkan pengajuan daur ulang ke jadwal penjemputan yang dipilih warga.
 * Nullable agar pengajuan dengan metode "antar mandiri" tidak wajib punya jadwal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_daur_ulang', function (Blueprint $table) {
            $table->foreignId('jadwal_id')
                ->nullable()
                ->after('wilayah_id')
                ->constrained('jadwal_penjemputan')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_daur_ulang', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jadwal_id');
        });
    }
};
