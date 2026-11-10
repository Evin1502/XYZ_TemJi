<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan_iurans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_warga');
            $table->string('wilayah');
            $table->string('bulan_tagihan'); // Contoh: "November 2026"
            $table->decimal('nominal_asal', 12, 2); // Rp 20.000
            $table->integer('poin_digunakan')->default(0); // Misal: 100 poin
            $table->decimal('potongan_poin', 12, 2)->default(0); // Rp 10.000
            $table->decimal('total_dibayar', 12, 2); // Rp 10.000
            $table->date('jatuh_tempo');
            $table->enum('status', ['Belum dibayar', 'Lunas'])->default('Belum dibayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan_iurans');
    }
};