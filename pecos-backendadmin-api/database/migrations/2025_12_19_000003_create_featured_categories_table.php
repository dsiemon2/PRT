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
        Schema::create('featured_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('category_id');
            $table->string('label', 100);
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique('category_id');
            $table->index('sort_order');
        });

        // Add setting for featured categories visibility (settings table has no timestamps)
        DB::table('settings')->insert([
            'setting_group' => 'featured_categories',
            'setting_key' => 'featured_categories_visible',
            'setting_value' => 'true',
            'setting_type' => 'boolean',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('featured_categories');

        DB::table('settings')
            ->where('setting_group', 'featured_categories')
            ->delete();
    }
};
