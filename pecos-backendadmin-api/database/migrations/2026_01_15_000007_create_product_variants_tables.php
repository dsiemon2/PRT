<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Product Attribute Types (e.g., Size, Color, Material)
        Schema::create('product_attribute_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('display_type')->default('dropdown'); // dropdown, swatch, radio, buttons
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_variation')->default(true); // Does this attribute create variants?
            $table->boolean('is_filterable')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Product Attribute Values (e.g., Small, Medium, Large for Size)
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_type_id')->constrained('product_attribute_types')->cascadeOnDelete();
            $table->string('value');
            $table->string('label')->nullable();
            $table->string('swatch_value')->nullable(); // Color hex, image URL, etc.
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Product Variants (SKUs)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->string('product_upc', 50)->index(); // References products.UPC
            $table->string('sku')->unique();
            $table->string('name')->nullable();
            $table->decimal('price', 10, 2)->nullable(); // Override base price
            $table->decimal('compare_price', 10, 2)->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('weight_unit')->default('lb');
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->string('dimension_unit')->default('in');
            $table->string('barcode')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('track_inventory')->default(true);
            $table->boolean('allow_backorder')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['product_upc', 'is_active']);
        });

        // Variant Attribute Values (links variants to attribute values)
        Schema::create('variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_type_id')->constrained('product_attribute_types')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('product_attribute_values')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['variant_id', 'attribute_type_id']);
        });

        // Variant Images
        Schema::create('variant_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('image_url');
            $table->string('alt_text')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Product Attribute Assignments (which attributes apply to which products)
        Schema::create('product_attribute_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('product_upc', 50)->index(); // References products.UPC
            $table->foreignId('attribute_type_id')->constrained('product_attribute_types')->cascadeOnDelete();
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['product_upc', 'attribute_type_id'], 'paa_upc_attr_type_unique');
        });

        // Variant Price Rules (bulk pricing, date-based pricing)
        Schema::create('variant_price_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('name');
            $table->string('rule_type'); // quantity_discount, customer_group, date_range
            $table->integer('min_quantity')->nullable();
            $table->integer('max_quantity')->nullable();
            $table->string('customer_group')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0);
            $table->timestamps();
        });

        // Variant Inventory Log
        Schema::create('variant_inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->string('action'); // add, remove, adjust, reserve, release
            $table->integer('quantity_change');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->string('reference_type')->nullable(); // order, return, adjustment, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->index(['variant_id', 'created_at']);
        });

        // Note: has_variants and variant_display_type columns should be added to products table manually
        // if needed, as the products table may have a different structure (using UPC instead of id)
    }

    public function down(): void
    {

        Schema::dropIfExists('variant_inventory_logs');
        Schema::dropIfExists('variant_price_rules');
        Schema::dropIfExists('product_attribute_assignments');
        Schema::dropIfExists('variant_images');
        Schema::dropIfExists('variant_attribute_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attribute_types');
    }
};
