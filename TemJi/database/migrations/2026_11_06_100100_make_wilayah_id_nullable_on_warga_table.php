<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('warga', function (Blueprint $table) {
            // PBI 1: warga lama yang wilayahnya belum lengkap.
            // Pendaftaran baru tetap wajib wilayah lewat RegistrasiWargaRequest.
            $table->unsignedBigInteger('wilayah_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('warga', function (Blueprint $table) {
            $table->unsignedBigInteger('wilayah_id')->nullable(false)->change();
        });
    }
};
