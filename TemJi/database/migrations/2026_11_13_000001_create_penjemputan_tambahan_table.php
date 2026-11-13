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
        Schema::create('penjemputan_tambahan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique(); // mis. PT-2611-0001
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('wilayah_id')->constrained('wilayah')->cascadeOnDelete();
            $table->date('tanggal_diminta');
            $table->text('alasan');
            $table->string('status', 20)->default('menunggu'); // menunggu, disetujui, ditolak, dibatalkan
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjemputan_tambahan');
    }
};
