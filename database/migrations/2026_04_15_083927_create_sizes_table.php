<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('label');       // "42", "9", "XL"
            $table->string('system');      // "EU", "US", "UK"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['label', 'system']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sizes');
    }
};