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
        Schema::create('riwayat_poin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('warga')->cascadeOnDelete()->comment('Warga pemilik poin');
            $table->enum('jenis', ['masuk', 'keluar'])->comment('masuk = setoran daur ulang, keluar = penukaran potongan iuran');
            $table->unsignedInteger('jumlah_poin')->comment('Nominal mutasi poin');
            $table->unsignedInteger('saldo_akhir')->comment('Snapshot saldo poin warga setelah transaksi ini');
            $table->string('judul', 150)->comment('Nama transaksi (contoh: Minyak jelantah 2,4 kg, Potongan iuran Oktober)');
            $table->string('keterangan', 100)->nullable()->comment('Keterangan ringkas (contoh: DU-2610-0187 · 22 Okt, Ditukar · 5 Okt)');
            $table->string('kode_pengajuan', 30)->nullable()->comment('Kode pengajuan daur ulang jika berasal dari setoran');
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_daur_ulang')->nullOnDelete()->comment('Kategori daur ulang jika transaksi penambahan');
            $table->decimal('berat_kg', 8, 2)->nullable()->comment('Berat kg yang ditimbang jika transaksi penambahan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_poin');
    }
};
