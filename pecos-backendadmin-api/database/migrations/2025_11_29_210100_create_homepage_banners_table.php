<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('subtitle', 500)->nullable();
            $table->string('desktop_image', 500); // path to desktop image
            $table->string('mobile_image', 500)->nullable(); // optional mobile image
            $table->string('link_url', 500)->nullable();
            $table->string('link_text', 100)->nullable(); // e.g., "Shop Now"
            $table->string('alt_text', 200)->nullable();
            $table->enum('position', ['full', 'left', 'center', 'right'])->default('full');
            $table->enum('text_position', ['left', 'center', 'right'])->default('center');
            $table->string('overlay_color', 50)->default('rgba(0,0,0,0.3)');
            $table->string('text_color', 7)->default('#FFFFFF');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });

        // Banner carousel settings
        Schema::create('banner_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('carousel_enabled')->default(true);
            $table->integer('slide_duration')->default(5); // seconds
            $table->boolean('show_indicators')->default(true);
            $table->boolean('show_controls')->default(true);
            $table->enum('transition', ['slide', 'fade'])->default('slide');
            $table->integer('banner_height')->default(400); // pixels
            $table->integer('mobile_banner_height')->default(250); // pixels
            $table->timestamps();
        });

        // Insert default settings
        DB::table('banner_settings')->insert([
            'carousel_enabled' => true,
            'slide_duration' => 5,
            'show_indicators' => true,
            'show_controls' => true,
            'transition' => 'slide',
            'banner_height' => 400,
            'mobile_banner_height' => 250,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('banner_settings');
        Schema::dropIfExists('homepage_banners');
    }
};
