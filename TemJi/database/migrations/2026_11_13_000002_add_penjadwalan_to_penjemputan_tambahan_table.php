<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('penjemputan_tambahan', function (Blueprint $table) {
            // Diisi admin saat menyetujui: masuk ke jadwal petugas
            $table->foreignId('petugas_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->date('tanggal_jemput')->nullable()->after('petugas_id');
            $table->time('jam_mulai')->nullable()->after('tanggal_jemput');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
            // Diisi admin saat menolak
            $table->text('alasan_penolakan')->nullable()->after('jam_selesai');
            $table->foreignId('diproses_oleh')->nullable()->after('alasan_penolakan')->constrained('users')->nullOnDelete();
            $table->timestamp('diproses_pada')->nullable()->after('diproses_oleh');

            $table->index(['petugas_id', 'tanggal_jemput']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjemputan_tambahan', function (Blueprint $table) {
            $table->dropIndex(['petugas_id', 'tanggal_jemput']);
            $table->dropConstrainedForeignId('petugas_id');
            $table->dropConstrainedForeignId('diproses_oleh');
            $table->dropColumn(['tanggal_jemput', 'jam_mulai', 'jam_selesai', 'alasan_penolakan', 'diproses_pada']);
        });
    }
};
