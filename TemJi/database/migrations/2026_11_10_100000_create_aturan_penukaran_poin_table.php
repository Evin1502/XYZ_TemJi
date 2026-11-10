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
        Schema::create('aturan_penukaran_poin', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('nilai_rupiah_per_poin')->default(100)->comment('Nilai rupiah tiap 1 poin daur ulang');
            $table->unsignedTinyInteger('batas_maksimal_potongan_persen')->default(50)->comment('Batas maksimal % potongan terhadap total tagihan iuran');
            $table->unsignedInteger('minimal_poin_tukar')->default(50)->comment('Minimal poin yang dapat ditukarkan');
            $table->date('berlaku_mulai')->comment('Tanggal mulai berlakunya aturan penukaran');
            $table->date('berlaku_sampai')->nullable()->comment('Tanggal berakhirnya aturan penukaran (null jika sedang berlaku aktif)');
            $table->string('label_periode', 100)->nullable()->comment('Label teks periode berlaku untuk tampilan UI');
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->nullOnDelete()->comment('Admin yang menetapkan aturan ini');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aturan_penukaran_poin');
    }
};
