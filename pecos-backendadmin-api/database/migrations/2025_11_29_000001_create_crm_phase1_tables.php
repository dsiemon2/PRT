<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CRM Phase 1 Tables
 *
 * Creates tables for:
 * 1. Customer Tags (manual and auto tags)
 * 2. Customer Tag Assignments
 * 3. Customer Activities/Timeline
 * 4. Customer Notes
 * 5. Customer Communications Log
 * 6. Customer Segments
 * 7. Customer Segment Members
 * 8. Customer Metrics (cached calculations)
 */
return new class extends Migration
{
    public function up(): void
    {
        // Customer Tags - for labeling customers
        Schema::create('customer_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('color', 7)->default('#6c757d');
            $table->text('description')->nullable();
            $table->boolean('is_auto')->default(false); // Auto-generated vs manual
            $table->string('auto_criteria')->nullable(); // JSON criteria for auto tags
            $table->integer('usage_count')->default(0);
            $table->timestamps();

            $table->unique('name');
        });

        // Customer Tag Assignments
        Schema::create('customer_tag_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('assigned_at')->useCurrent();

            $table->unique(['customer_id', 'tag_id']);
            $table->index('customer_id');
            $table->index('tag_id');
        });

        // Customer Activities - timeline of all customer interactions
        Schema::create('customer_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->enum('activity_type', [
                'order', 'email', 'support', 'review', 'loyalty',
                'login', 'note', 'wishlist', 'cart', 'account', 'other'
            ]);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Additional data like order_id, email_id, etc.
            $table->unsignedBigInteger('created_by')->nullable(); // Admin who triggered it
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
            $table->index('activity_type');
        });

        // Customer Notes - internal notes about customers
        Schema::create('customer_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->text('note');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_private')->default(false); // Only visible to author
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->index('customer_id');
        });

        // Customer Communications - log of all communications
        Schema::create('customer_communications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->enum('type', ['email', 'sms', 'chat', 'phone', 'social', 'internal']);
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('subject', 255)->nullable();
            $table->text('content')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->enum('status', [
                'draft', 'scheduled', 'sent', 'delivered',
                'opened', 'clicked', 'bounced', 'failed'
            ])->default('sent');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->json('metadata')->nullable(); // Email headers, tracking info, etc.
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'created_at']);
            $table->index('type');
            $table->index('status');
        });

        // Customer Segments - for grouping customers
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->json('rules'); // Segment criteria as JSON
            $table->boolean('is_dynamic')->default(true); // Auto-update vs static
            $table->boolean('is_preset')->default(false); // Built-in vs custom
            $table->integer('customer_count')->default(0);
            $table->timestamp('last_calculated')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique('name');
        });

        // Customer Segment Members
        Schema::create('customer_segment_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('segment_id');
            $table->unsignedBigInteger('customer_id');
            $table->timestamp('added_at')->useCurrent();

            $table->unique(['segment_id', 'customer_id']);
            $table->index('customer_id');
        });

        // Customer Metrics - cached customer analytics
        Schema::create('customer_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->unique();
            $table->decimal('lifetime_value', 10, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('avg_order_value', 10, 2)->default(0);
            $table->date('first_order_date')->nullable();
            $table->date('last_order_date')->nullable();
            $table->integer('days_since_last_order')->nullable();
            $table->decimal('purchase_frequency', 5, 2)->nullable(); // Orders per month
            $table->integer('rfm_recency_score')->nullable(); // 1-5
            $table->integer('rfm_frequency_score')->nullable(); // 1-5
            $table->integer('rfm_monetary_score')->nullable(); // 1-5
            $table->string('rfm_segment', 50)->nullable(); // Champion, Loyal, etc.
            $table->decimal('churn_risk_score', 3, 2)->nullable(); // 0.00 - 1.00
            $table->integer('health_score')->nullable(); // 1-100
            $table->integer('email_open_rate')->nullable(); // Percentage
            $table->integer('email_click_rate')->nullable(); // Percentage
            $table->timestamp('calculated_at')->nullable();

            $table->index('rfm_segment');
            $table->index('churn_risk_score');
            $table->index('health_score');
        });

        // Email Templates - for quick communications
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('category', ['order', 'service', 'marketing', 'transactional', 'personal']);
            $table->string('subject', 255);
            $table->text('body_html');
            $table->text('body_text')->nullable();
            $table->json('variables')->nullable(); // Available merge variables
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
        Schema::dropIfExists('customer_metrics');
        Schema::dropIfExists('customer_segment_members');
        Schema::dropIfExists('customer_segments');
        Schema::dropIfExists('customer_communications');
        Schema::dropIfExists('customer_notes');
        Schema::dropIfExists('customer_activities');
        Schema::dropIfExists('customer_tag_assignments');
        Schema::dropIfExists('customer_tags');
    }
};
