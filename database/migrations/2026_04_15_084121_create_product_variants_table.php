<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('size_id')
                  ->constrained()
                  ->restrictOnDelete();
            $table->foreignId('color_id')
                  ->constrained()
                  ->restrictOnDelete();
            $table->string('sku')->unique();
            $table->decimal('price_override', 10, 2)->nullable(); // null = use product base_price
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // A product cannot have two variants with the same size + color combo
            $table->unique(['product_id', 'size_id', 'color_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};