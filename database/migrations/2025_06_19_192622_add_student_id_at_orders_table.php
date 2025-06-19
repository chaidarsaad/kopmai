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
            // Tambahkan kolom student_id terlebih dahulu
            $table->unsignedBigInteger('student_id')->nullable()->after('user_id');

            // Tambahkan foreign key constraint
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus foreign key constraint dulu
            $table->dropForeign(['student_id']);

            // Lalu hapus kolomnya
            $table->dropColumn('student_id');
        });
    }
};
