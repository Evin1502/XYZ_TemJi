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
        Schema::create('jadwal_penjemputan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wilayah_id')->constrained('wilayah')->cascadeOnDelete();
            $table->foreignId('petugas_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis', ['sampah_biasa', 'daur_ulang']);
            $table->unsignedTinyInteger('hari'); // 1 = Senin ... 6 = Sabtu
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedInteger('kuota')->nullable(); // hanya untuk daur ulang (jumlah rumah)
            $table->timestamps();

            $table->index(['petugas_id', 'hari']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_penjemputan');
    }
};
