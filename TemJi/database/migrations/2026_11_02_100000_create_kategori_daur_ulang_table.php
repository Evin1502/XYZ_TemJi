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
        Schema::create('kategori_daur_ulang', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('tipe'); // Minyak jelantah, Botol plastik
            $table->string('satuan')->default('kg');
            $table->string('icon')->default('bottle'); // droplet, bottle, cup
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_daur_ulang');
    }
};
