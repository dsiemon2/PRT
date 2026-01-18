<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->string('icon', 50)->nullable(); // bi-phone, bi-truck, bi-gift, etc.
            $table->string('link_url', 500)->nullable();
            $table->string('link_text', 100)->nullable();
            $table->enum('position', ['left', 'center', 'right'])->default('center');
            $table->string('bg_color', 7)->default('#C41E3A');
            $table->string('text_color', 7)->default('#FFFFFF');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();
        });

        // Global announcement settings
        Schema::create('announcement_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->boolean('allow_dismiss')->default(true);
            $table->integer('rotation_speed')->default(5); // seconds
            $table->enum('animation', ['fade', 'slide', 'none'])->default('fade');
            $table->timestamps();
        });

        // Insert default settings
        DB::table('announcement_settings')->insert([
            'enabled' => false,
            'allow_dismiss' => true,
            'rotation_speed' => 5,
            'animation' => 'fade',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_settings');
        Schema::dropIfExists('announcements');
    }
};
