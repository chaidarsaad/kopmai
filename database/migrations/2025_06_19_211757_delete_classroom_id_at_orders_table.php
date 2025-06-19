<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['classroom_id']);

            // Setelah foreign key dihapus, baru kolom bisa di-drop
            $table->dropColumn('classroom_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('classroom_id')->nullable()->after('user_id');

            // Tambahkan kembali foreign key jika diperlukan
            $table->foreign('classroom_id')->references('id')->on('classrooms');
        });
    }
};
