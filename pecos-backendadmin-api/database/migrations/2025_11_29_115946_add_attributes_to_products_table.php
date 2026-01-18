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
        Schema::table('products3', function (Blueprint $table) {
            // ItemSize already exists in products3, add color and material
            $table->string('color', 50)->nullable()->after('ItemSize');
            $table->string('material', 100)->nullable()->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products3', function (Blueprint $table) {
            $table->dropColumn(['color', 'material']);
        });
    }
};
