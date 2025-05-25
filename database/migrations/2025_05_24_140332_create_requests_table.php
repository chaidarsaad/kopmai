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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('request_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
            $table->date('tanggal_permohonan');
            $table->string('nama_pemesan');
            $table->string('kelas_divisi');
            $table->string('nama_barang');
            $table->integer('jumlah_barang');
            $table->text('tujuan');
            $table->string('sumber_dana');
            $table->integer('budget'); // bisa juga pakai decimal jika mau desimal
            $table->date('deadline');
            $table->enum('status', [
                'Menunggu Verifikasi',
                'Sedang Proses Pengadaan',
                'Selesai',
                'Pengajuan Ditolak'
            ])->default('Menunggu Verifikasi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
