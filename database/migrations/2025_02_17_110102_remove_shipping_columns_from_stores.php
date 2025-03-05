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
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['shipping_provider', 'shipping_api_key', 'shipping_area_id', 'area_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->text('shipping_provider')->nullable();
            $table->text('shipping_api_key')->nullable();
            $table->text('shipping_area_id')->nullable();
            $table->string('area_name')->nullable();
        });
    }
};
