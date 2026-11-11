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
        Schema::create('pengajuan_daur_ulang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pengajuan', 20)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayah')->nullOnDelete();
            $table->unsignedInteger('total_estimasi_kg')->default(0);
            $table->unsignedInteger('total_estimasi_poin')->default(0);
            $table->string('status', 30)->default('menunggu_jadwal');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('pengajuan_daur_ulang_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_id')->constrained('pengajuan_daur_ulang')->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori_daur_ulang')->restrictOnDelete();
            $table->unsignedInteger('estimasi_berat_kg');
            $table->unsignedInteger('poin_per_kg_snapshot')->default(0);
            $table->unsignedInteger('estimasi_poin')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_daur_ulang_items');
        Schema::dropIfExists('pengajuan_daur_ulang');
    }
};
