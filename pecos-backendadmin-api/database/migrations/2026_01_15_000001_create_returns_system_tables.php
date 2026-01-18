<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Return Reasons
        Schema::create('return_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('requires_photo')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Returns (RMA)
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('rma_number')->unique();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('reason_id');
            $table->enum('status', ['pending', 'approved', 'rejected', 'received', 'inspecting', 'processed', 'refunded', 'exchanged', 'closed'])->default('pending');
            $table->enum('type', ['refund', 'exchange', 'store_credit'])->default('refund');
            $table->text('customer_notes')->nullable();
            $table->text('admin_notes')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->decimal('restocking_fee', 10, 2)->default(0);
            $table->string('refund_method')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('return_label_url')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedBigInteger('processed_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Return Items
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->unsignedBigInteger('order_item_id')->nullable();
            $table->string('product_upc');
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->enum('condition', ['unopened', 'like_new', 'good', 'fair', 'damaged', 'defective'])->default('good');
            $table->text('condition_notes')->nullable();
            $table->boolean('restock')->default(true);
            $table->timestamps();

            $table->foreign('return_id')->references('id')->on('returns')->onDelete('cascade');
        });

        // Return Photos
        Schema::create('return_photos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->unsignedBigInteger('return_item_id')->nullable();
            $table->string('photo_url');
            $table->string('photo_type')->default('condition');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('return_id')->references('id')->on('returns')->onDelete('cascade');
        });

        // Return Status History
        Schema::create('return_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('return_id');
            $table->string('old_status')->nullable();
            $table->string('new_status');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->timestamps();

            $table->foreign('return_id')->references('id')->on('returns')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_status_history');
        Schema::dropIfExists('return_photos');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('returns');
        Schema::dropIfExists('return_reasons');
    }
};
