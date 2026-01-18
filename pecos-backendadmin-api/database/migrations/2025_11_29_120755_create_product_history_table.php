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
        Schema::create('product_history', function (Blueprint $table) {
            $table->id();
            $table->double('product_id'); // References products3.ID
            $table->string('upc', 50)->nullable(); // Product UPC for easier lookup
            $table->string('field_name', 100); // Name of field that changed
            $table->text('old_value')->nullable(); // Previous value
            $table->text('new_value')->nullable(); // New value
            $table->string('action', 50)->default('update'); // create, update, delete, stock_adjustment, image_upload, etc.
            $table->unsignedBigInteger('user_id')->nullable(); // Who made the change
            $table->string('user_name', 255)->nullable(); // Name of user for display
            $table->ipAddress('ip_address')->nullable();
            $table->text('notes')->nullable(); // Optional notes about the change
            $table->timestamp('created_at')->useCurrent();

            $table->index('product_id');
            $table->index('upc');
            $table->index('created_at');
            $table->index('action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_history');
    }
};
