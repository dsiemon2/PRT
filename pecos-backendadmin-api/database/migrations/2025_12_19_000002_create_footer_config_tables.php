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
        // Footer columns (Shop, Resources, Customer Service, Newsletter)
        Schema::create('footer_columns', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->integer('position')->default(1); // 1-4
            $table->boolean('is_visible')->default(true);
            $table->string('column_type', 20)->default('links'); // links, newsletter, custom
            $table->timestamps();

            $table->unique('position');
        });

        // Footer links within each column
        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('column_id')->constrained('footer_columns')->onDelete('cascade');
            $table->string('label', 100);
            $table->string('url', 255);
            $table->string('icon', 50)->default('bi-chevron-right');
            $table->string('feature_flag', 100)->nullable(); // e.g., 'tell_a_friend' - link only shows if feature enabled
            $table->string('link_type', 20)->default('internal'); // internal, external, page
            $table->integer('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_core')->default(false); // Core links cannot be deleted
            $table->timestamps();

            $table->index(['column_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('footer_links');
        Schema::dropIfExists('footer_columns');
    }
};
