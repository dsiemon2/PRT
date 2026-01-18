<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Search Facets Configuration
        Schema::create('search_facets', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Display name
            $table->string('code')->unique(); // Internal identifier
            $table->string('type'); // category, price_range, attribute, brand, rating, availability
            $table->string('attribute_name')->nullable(); // Product attribute to facet on
            $table->json('options')->nullable(); // Predefined options for the facet
            $table->boolean('is_active')->default(true);
            $table->boolean('is_collapsed')->default(false); // Default collapsed in UI
            $table->integer('sort_order')->default(0);
            $table->integer('max_options')->default(10); // Max options to show before "Show more"
            $table->boolean('show_count')->default(true); // Show result count per option
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // Search Synonyms
        Schema::create('search_synonyms', function (Blueprint $table) {
            $table->id();
            $table->string('term'); // Main term
            $table->text('synonyms'); // Comma-separated synonyms
            $table->boolean('is_bidirectional')->default(true); // Works both ways
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('term');
        });

        // Search Redirects
        Schema::create('search_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('search_term');
            $table->string('redirect_url');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('search_term');
        });

        // Search Boosts (promote certain products)
        Schema::create('search_boosts', function (Blueprint $table) {
            $table->id();
            $table->string('search_term');
            $table->string('product_upc', 50)->index(); // References products.UPC
            $table->integer('boost_value')->default(1); // Higher = more prominent
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['search_term', 'is_active']);
        });

        // Search Buried (hide certain products)
        Schema::create('search_buried', function (Blueprint $table) {
            $table->id();
            $table->string('search_term');
            $table->string('product_upc', 50)->index(); // References products.UPC
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['search_term', 'is_active']);
        });

        // Search Query Log
        Schema::create('search_queries', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->unsignedInteger('customer_id')->nullable()->index(); // References customers.CustomerID
            $table->integer('results_count')->default(0);
            $table->boolean('has_results')->default(true);
            $table->json('filters_applied')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['query', 'created_at']);
            $table->index('has_results');
        });

        // Popular Searches
        Schema::create('popular_searches', function (Blueprint $table) {
            $table->id();
            $table->string('query')->unique();
            $table->integer('search_count')->default(1);
            $table->integer('click_count')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['search_count', 'is_featured']);
        });

        // Search Autocomplete Suggestions
        Schema::create('search_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('suggestion');
            $table->string('type'); // product, category, brand, recent, popular
            $table->string('product_upc', 50)->nullable()->index(); // References products.UPC
            $table->integer('category_code')->nullable()->index(); // References categories.CategoryCode
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active', 'priority']);
            $table->fullText('suggestion');
        });

        // Search Filter Rules (dynamic filtering)
        Schema::create('search_filter_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('conditions'); // When to apply
            $table->json('filters'); // What filters to apply
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Search Analytics
        Schema::create('search_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('total_searches')->default(0);
            $table->integer('unique_searches')->default(0);
            $table->integer('zero_result_searches')->default(0);
            $table->integer('searches_with_clicks')->default(0);
            $table->decimal('avg_click_position', 5, 2)->nullable();
            $table->decimal('search_exit_rate', 5, 2)->nullable();
            $table->timestamps();

            $table->unique('date');
        });

        // Search Click Tracking
        Schema::create('search_clicks', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->string('product_upc', 50)->index(); // References products.UPC
            $table->unsignedInteger('customer_id')->nullable()->index(); // References customers.CustomerID
            $table->integer('position')->default(0); // Position in search results
            $table->timestamps();

            $table->index(['query', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_clicks');
        Schema::dropIfExists('search_analytics');
        Schema::dropIfExists('search_filter_rules');
        Schema::dropIfExists('search_suggestions');
        Schema::dropIfExists('popular_searches');
        Schema::dropIfExists('search_queries');
        Schema::dropIfExists('search_buried');
        Schema::dropIfExists('search_boosts');
        Schema::dropIfExists('search_redirects');
        Schema::dropIfExists('search_synonyms');
        Schema::dropIfExists('search_facets');
    }
};
