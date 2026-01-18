<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Currencies
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // ISO 4217 code (USD, EUR, GBP)
            $table->string('name');
            $table->string('symbol', 10);
            $table->string('symbol_position', 10)->default('before'); // before/after
            $table->integer('decimal_places')->default(2);
            $table->string('decimal_separator', 5)->default('.');
            $table->string('thousand_separator', 5)->default(',');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Exchange Rates
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('currency_id');
            $table->decimal('rate', 18, 8); // Rate relative to base currency
            $table->string('source')->default('manual'); // manual, api, etc.
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
        });

        // Exchange Rate History (for tracking)
        Schema::create('exchange_rate_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('currency_id');
            $table->decimal('rate', 18, 8);
            $table->string('source')->default('manual');
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->index(['currency_id', 'recorded_at']);
        });

        // Product Prices per Currency (optional, for fixed prices)
        Schema::create('product_currency_prices', function (Blueprint $table) {
            $table->id();
            $table->string('product_upc');
            $table->unsignedBigInteger('currency_id');
            $table->decimal('price', 12, 4);
            $table->decimal('sale_price', 12, 4)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            $table->unique(['product_upc', 'currency_id']);
        });

        // Currency Settings (stored in settings table)
        // - default_currency
        // - auto_update_rates
        // - rate_api_provider
        // - rate_update_frequency
    }

    public function down(): void
    {
        Schema::dropIfExists('product_currency_prices');
        Schema::dropIfExists('exchange_rate_history');
        Schema::dropIfExists('exchange_rates');
        Schema::dropIfExists('currencies');
    }
};
