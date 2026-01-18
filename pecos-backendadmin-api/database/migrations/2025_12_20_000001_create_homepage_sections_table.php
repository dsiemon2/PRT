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
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(); // Optional title displayed on frontend
            $table->string('admin_label')->nullable(); // Admin-only label for identification
            $table->longText('content'); // HTML content from TinyMCE
            $table->string('background_style')->default('white'); // white, cream, gradient, dark, custom
            $table->string('background_color')->nullable(); // For custom color option
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
