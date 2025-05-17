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
        Schema::table('users', function (Blueprint $table) {
            // Tambahkan kolom shop_id terlebih dahulu
            $table->unsignedBigInteger('shop_id')->nullable()->after('id');

            // Tambahkan foreign key constraint
            $table->foreign('shop_id')->references('id')->on('shops')->nullOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key constraint dulu
            $table->dropForeign(['shop_id']);

            // Lalu hapus kolomnya
            $table->dropColumn('shop_id');
        });
    }
};
