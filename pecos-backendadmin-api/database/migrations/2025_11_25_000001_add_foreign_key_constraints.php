<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds foreign key constraints to establish proper relationships
     * between tables. Note: Many tables already exist with data, so we're adding
     * constraints incrementally where data integrity allows.
     */
    public function up(): void
    {
        // User-related relationships
        Schema::table('user_addresses', function (Blueprint $table) {
            if (!$this->hasForeignKey('user_addresses', 'user_addresses_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        });

        Schema::table('user_payment_methods', function (Blueprint $table) {
            if (!$this->hasForeignKey('user_payment_methods', 'user_payment_methods_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
            if (!$this->hasForeignKey('user_payment_methods', 'user_payment_methods_billing_address_id_foreign')) {
                $table->foreign('billing_address_id')
                    ->references('id')
                    ->on('user_addresses')
                    ->onDelete('set null');
            }
        });

        Schema::table('user_delivery_preferences', function (Blueprint $table) {
            if (!$this->hasForeignKey('user_delivery_preferences', 'user_delivery_preferences_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        });

        Schema::table('user_notification_preferences', function (Blueprint $table) {
            if (!$this->hasForeignKey('user_notification_preferences', 'user_notification_preferences_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        });

        Schema::table('user_wishlists', function (Blueprint $table) {
            if (!$this->hasForeignKey('user_wishlists', 'user_wishlists_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        });

        Schema::table('user_gift_cards', function (Blueprint $table) {
            if (!$this->hasForeignKey('user_gift_cards', 'user_gift_cards_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        });

        // Order relationships
        Schema::table('orders', function (Blueprint $table) {
            if (!$this->hasForeignKey('orders', 'orders_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!$this->hasForeignKey('order_items', 'order_items_order_id_foreign')) {
                $table->foreign('order_id')
                    ->references('id')
                    ->on('orders')
                    ->onDelete('cascade');
            }
        });

        Schema::table('order_status_history', function (Blueprint $table) {
            if (!$this->hasForeignKey('order_status_history', 'order_status_history_order_id_foreign')) {
                $table->foreign('order_id')
                    ->references('id')
                    ->on('orders')
                    ->onDelete('cascade');
            }
        });

        // Product relationships
        Schema::table('product_reviews', function (Blueprint $table) {
            if (!$this->hasForeignKey('product_reviews', 'product_reviews_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            }
        });

        // Supplier and Purchase Order relationships
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!$this->hasForeignKey('purchase_orders', 'purchase_orders_supplier_id_foreign')) {
                $table->foreign('supplier_id')
                    ->references('id')
                    ->on('suppliers')
                    ->onDelete('set null');
            }
            if (!$this->hasForeignKey('purchase_orders', 'purchase_orders_dropshipper_id_foreign')) {
                $table->foreign('dropshipper_id')
                    ->references('id')
                    ->on('dropshippers')
                    ->onDelete('set null');
            }
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!$this->hasForeignKey('purchase_order_items', 'purchase_order_items_purchase_order_id_foreign')) {
                $table->foreign('purchase_order_id')
                    ->references('id')
                    ->on('purchase_orders')
                    ->onDelete('cascade');
            }
        });

        // Shipping relationships
        Schema::table('shipping_methods', function (Blueprint $table) {
            if (!$this->hasForeignKey('shipping_methods', 'shipping_methods_zone_id_foreign')) {
                $table->foreign('zone_id')
                    ->references('id')
                    ->on('shipping_zones')
                    ->onDelete('cascade');
            }
        });

        // Tax relationships
        Schema::table('tax_exemptions', function (Blueprint $table) {
            if (!$this->hasForeignKey('tax_exemptions', 'tax_exemptions_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        });

        // Gift card relationships
        Schema::table('gift_card_transactions', function (Blueprint $table) {
            if (!$this->hasForeignKey('gift_card_transactions', 'gift_card_transactions_gift_card_id_foreign')) {
                $table->foreign('gift_card_id')
                    ->references('id')
                    ->on('gift_cards')
                    ->onDelete('cascade');
            }
        });

        // Loyalty relationships
        Schema::table('loyalty_members', function (Blueprint $table) {
            if (!$this->hasForeignKey('loyalty_members', 'loyalty_members_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
            if (!$this->hasForeignKey('loyalty_members', 'loyalty_members_tier_id_foreign')) {
                $table->foreign('tier_id')
                    ->references('id')
                    ->on('loyalty_tiers')
                    ->onDelete('set null');
            }
        });

        Schema::table('loyalty_transactions', function (Blueprint $table) {
            if (!$this->hasForeignKey('loyalty_transactions', 'loyalty_transactions_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            }
        });

        // Dropshipper relationships
        Schema::table('dropship_orders', function (Blueprint $table) {
            if (!$this->hasForeignKey('dropship_orders', 'dropship_orders_dropshipper_id_foreign')) {
                $table->foreign('dropshipper_id')
                    ->references('id')
                    ->on('dropshippers')
                    ->onDelete('cascade');
            }
        });

        Schema::table('dropship_order_items', function (Blueprint $table) {
            if (!$this->hasForeignKey('dropship_order_items', 'dropship_order_items_order_id_foreign')) {
                $table->foreign('order_id')
                    ->references('id')
                    ->on('dropship_orders')
                    ->onDelete('cascade');
            }
        });

        Schema::table('api_logs', function (Blueprint $table) {
            if (!$this->hasForeignKey('api_logs', 'api_logs_dropshipper_id_foreign')) {
                $table->foreign('dropshipper_id')
                    ->references('id')
                    ->on('dropshippers')
                    ->onDelete('set null');
            }
        });

        // Coupon relationships
        Schema::table('coupon_usage', function (Blueprint $table) {
            if (!$this->hasForeignKey('coupon_usage', 'coupon_usage_coupon_id_foreign')) {
                $table->foreign('coupon_id')
                    ->references('id')
                    ->on('coupons')
                    ->onDelete('cascade');
            }
            if (!$this->hasForeignKey('coupon_usage', 'coupon_usage_user_id_foreign')) {
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            }
        });

        // Blog relationships
        Schema::table('blog_posts', function (Blueprint $table) {
            if (!$this->hasForeignKey('blog_posts', 'blog_posts_category_id_foreign')) {
                $table->foreign('category_id')
                    ->references('id')
                    ->on('blog_categories')
                    ->onDelete('set null');
            }
        });

        Schema::table('blog_post_tags', function (Blueprint $table) {
            if (!$this->hasForeignKey('blog_post_tags', 'blog_post_tags_post_id_foreign')) {
                $table->foreign('post_id')
                    ->references('id')
                    ->on('blog_posts')
                    ->onDelete('cascade');
            }
            if (!$this->hasForeignKey('blog_post_tags', 'blog_post_tags_tag_id_foreign')) {
                $table->foreign('tag_id')
                    ->references('id')
                    ->on('blog_tags')
                    ->onDelete('cascade');
            }
        });

        // FAQ relationships
        Schema::table('faqs', function (Blueprint $table) {
            if (!$this->hasForeignKey('faqs', 'faqs_category_id_foreign')) {
                $table->foreign('category_id')
                    ->references('id')
                    ->on('faq_categories')
                    ->onDelete('set null');
            }
        });

        // Review votes relationships
        Schema::table('review_votes', function (Blueprint $table) {
            if (!$this->hasForeignKey('review_votes', 'review_votes_review_id_foreign')) {
                $table->foreign('review_id')
                    ->references('id')
                    ->on('product_reviews')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys in reverse order
        $tables = [
            'review_votes' => ['review_id'],
            'faqs' => ['category_id'],
            'blog_post_tags' => ['post_id', 'tag_id'],
            'blog_posts' => ['category_id'],
            'coupon_usage' => ['coupon_id', 'user_id'],
            'api_logs' => ['dropshipper_id'],
            'dropship_order_items' => ['order_id'],
            'dropship_orders' => ['dropshipper_id'],
            'loyalty_transactions' => ['user_id'],
            'loyalty_members' => ['user_id', 'tier_id'],
            'gift_card_transactions' => ['gift_card_id'],
            'tax_exemptions' => ['user_id'],
            'shipping_methods' => ['zone_id'],
            'purchase_order_items' => ['purchase_order_id'],
            'purchase_orders' => ['supplier_id', 'dropshipper_id'],
            'product_reviews' => ['user_id'],
            'order_status_history' => ['order_id'],
            'order_items' => ['order_id'],
            'orders' => ['user_id'],
            'user_gift_cards' => ['user_id'],
            'user_wishlists' => ['user_id'],
            'user_notification_preferences' => ['user_id'],
            'user_delivery_preferences' => ['user_id'],
            'user_payment_methods' => ['user_id', 'billing_address_id'],
            'user_addresses' => ['user_id'],
        ];

        foreach ($tables as $table => $columns) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $blueprint) use ($table, $columns) {
                    foreach ($columns as $column) {
                        $foreignKey = "{$table}_{$column}_foreign";
                        if ($this->hasForeignKey($table, $foreignKey)) {
                            $blueprint->dropForeign($foreignKey);
                        }
                    }
                });
            }
        }
    }

    /**
     * Check if a foreign key exists on a table.
     */
    private function hasForeignKey(string $table, string $foreignKey): bool
    {
        $database = config('database.connections.mysql.database');

        $result = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = ?
            AND TABLE_NAME = ?
            AND CONSTRAINT_NAME = ?
            AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ", [$database, $table, $foreignKey]);

        return $result[0]->count > 0;
    }
};
