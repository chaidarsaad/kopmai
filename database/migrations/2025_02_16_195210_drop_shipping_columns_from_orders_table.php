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
            $table->dropColumn([
                'shipping_provider',
                'shipping_cost',
                'shipping_area_id',
                'shipping_area_name',
                'shipping_address',
                'shipping_method_detail',
                'shipping_tracking_number',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_provider')->nullable();
            $table->integer('shipping_cost')->default(0);
            $table->string('shipping_area_id')->nullable();
            $table->string('shipping_area_name')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_method_detail')->nullable();
            $table->string('shipping_tracking_number')->nullable();
        });
    }
};
