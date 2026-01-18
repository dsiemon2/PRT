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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_group', 50)->index();
            $table->string('setting_key', 100);
            $table->text('setting_value')->nullable();
            $table->string('setting_type', 20)->default('string'); // string, boolean, number, json
            $table->timestamp('updated_at')->nullable();

            $table->unique(['setting_group', 'setting_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
