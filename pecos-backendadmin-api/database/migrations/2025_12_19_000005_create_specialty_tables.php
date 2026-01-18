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
        // Specialty Categories table
        Schema::create('specialty_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->nullable(); // References categories.CategoryCode (can be null for custom categories)
            $table->string('label', 255);
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index('category_id');
            $table->index('sort_order');
        });

        // Specialty Products table
        Schema::create('specialty_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('specialty_category_id');
            $table->string('upc', 50)->nullable(); // References products3.UPC (can be null for custom products)
            $table->string('label', 255);
            $table->text('description')->nullable();
            $table->string('sizes', 500)->nullable(); // Comma-separated sizes
            $table->string('colors', 500)->nullable(); // Comma-separated colors
            $table->decimal('price', 10, 2)->nullable(); // Override price (null = use product price)
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->foreign('specialty_category_id')
                  ->references('id')
                  ->on('specialty_categories')
                  ->onDelete('cascade');

            $table->index('upc');
            $table->index('sort_order');
        });

        // Add specialty settings
        DB::table('settings')->insert([
            ['setting_group' => 'specialty', 'setting_key' => 'specialty_products_title', 'setting_value' => 'Special Products', 'setting_type' => 'string'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialty_products');
        Schema::dropIfExists('specialty_categories');

        DB::table('settings')->where('setting_group', 'specialty')->delete();
    }
};
