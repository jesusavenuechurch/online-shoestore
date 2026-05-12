<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lookup table for payment methods
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // Cash, M-Pesa, EcoCash, Bank Transfer
            $table->string('code');        // cash, mpesa, ecocash, bank
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Add to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('payment_method_id')
                  ->nullable()
                  ->after('status_id')
                  ->constrained('payment_methods')
                  ->nullOnDelete();

            $table->string('payment_reference')->nullable()->after('payment_method_id');
            // M-Pesa transaction ID, bank ref etc — optional, good to have
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropColumn('payment_reference');
        });
        Schema::dropIfExists('payment_methods');
    }
};