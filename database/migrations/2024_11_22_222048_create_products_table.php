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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('name');
            $table->string('slug')->unique();
            $table->longtext('description')->nullable();
            $table->integer('price');
            $table->integer('stock');
            $table->boolean('is_active')->default(true);
            $table->decimal('weight', 10, 2)->default(0)->comment('Weight in grams');
            $table->decimal('height', 10, 2)->default(0)->comment('Height in cm');
            $table->decimal('width', 10, 2)->default(0)->comment('Width in cm');
            $table->decimal('length', 10, 2)->default(0)->comment('Length in cm');
            $table->string('images')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
