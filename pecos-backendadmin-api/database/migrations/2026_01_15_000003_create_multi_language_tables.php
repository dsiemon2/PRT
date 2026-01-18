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
        // Languages table
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // ISO 639-1 code (e.g., 'en', 'es', 'fr')
            $table->string('locale', 20)->unique(); // Full locale (e.g., 'en_US', 'es_MX')
            $table->string('name'); // Language name in English
            $table->string('native_name'); // Language name in its own language
            $table->string('flag_icon', 50)->nullable(); // Flag emoji or icon class
            $table->string('direction', 3)->default('ltr'); // 'ltr' or 'rtl'
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Translation keys - master list of all translatable strings
        Schema::create('translation_keys', function (Blueprint $table) {
            $table->id();
            $table->string('group', 100)->index(); // Group name (e.g., 'products', 'checkout', 'emails')
            $table->string('key'); // Translation key (e.g., 'add_to_cart', 'checkout_title')
            $table->text('description')->nullable(); // Description for translators
            $table->boolean('is_html')->default(false); // Whether value can contain HTML
            $table->timestamps();

            $table->unique(['group', 'key']);
        });

        // Translations - actual translated values
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->foreignId('translation_key_id')->constrained()->onDelete('cascade');
            $table->text('value');
            $table->boolean('is_reviewed')->default(false); // Has been reviewed by a human
            $table->string('translated_by')->nullable(); // 'auto', 'human', email of translator
            $table->timestamps();

            $table->unique(['language_id', 'translation_key_id']);
        });

        // Product translations
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->string('product_upc', 50)->index(); // References products.UPC
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();

            $table->unique(['product_upc', 'language_id']);
        });

        // Category translations
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('category_code')->index(); // References categories.CategoryCode
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['category_code', 'language_id']);
        });

        // Email template translations
        Schema::create('email_template_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('email_template_id')->index();
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->text('body');
            $table->timestamps();

            $table->unique(['email_template_id', 'language_id']);
        });

        // Static page translations
        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id'); // Generic page ID from footer_pages or similar
            $table->string('page_type', 50)->default('footer'); // Type of page
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['page_id', 'page_type', 'language_id']);
            $table->index(['page_id', 'page_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_translations');
        Schema::dropIfExists('email_template_translations');
        Schema::dropIfExists('category_translations');
        Schema::dropIfExists('product_translations');
        Schema::dropIfExists('translations');
        Schema::dropIfExists('translation_keys');
        Schema::dropIfExists('languages');
    }
};
