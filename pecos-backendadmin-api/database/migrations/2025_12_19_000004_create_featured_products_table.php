<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('featured_products', function (Blueprint $table) {
            $table->id();
            $table->string('upc', 20);
            $table->string('label', 100);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique('upc');
            $table->index('sort_order');
        });

        // Add settings for featured products visibility and section title
        DB::table('settings')->insert([
            [
                'setting_group' => 'featured_products',
                'setting_key' => 'featured_products_visible',
                'setting_value' => 'false',
                'setting_type' => 'boolean',
            ],
            [
                'setting_group' => 'featured_products',
                'setting_key' => 'featured_products_title',
                'setting_value' => 'Featured Products',
                'setting_type' => 'string',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_products');

        DB::table('settings')
            ->where('setting_group', 'featured_products')
            ->delete();
    }
};
