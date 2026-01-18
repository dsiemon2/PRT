<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CRM Phase 3: Sales Pipeline, Advanced Analytics, Integration Hub
     */
    public function up(): void
    {
        // ===========================================
        // SALES PIPELINE
        // ===========================================

        // Lead sources (where leads come from)
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Leads (potential customers)
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_number')->unique();
            $table->unsignedInteger('customer_id')->nullable(); // Legacy customers table
            $table->foreignId('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();

            // Contact info (for leads without customer account)
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();

            // Lead details
            $table->enum('status', ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost', 'dormant'])->default('new');
            $table->enum('priority', ['low', 'medium', 'high', 'hot'])->default('medium');
            $table->decimal('estimated_value', 12, 2)->nullable();
            $table->integer('probability')->default(0); // 0-100%
            $table->date('expected_close_date')->nullable();

            // Assignment
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->text('notes')->nullable();

            // Scoring
            $table->integer('lead_score')->default(0);
            $table->json('score_breakdown')->nullable();

            // Tracking
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('qualified_at')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->string('lost_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'priority']);
            $table->index('assigned_to');
            $table->index('lead_score');
        });

        // Lead activities (calls, emails, meetings, notes)
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('type', ['call', 'email', 'meeting', 'note', 'task', 'status_change', 'other']);
            $table->string('subject');
            $table->text('description')->nullable();
            $table->enum('outcome', ['completed', 'no_answer', 'left_message', 'scheduled', 'cancelled', 'pending'])->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['lead_id', 'type']);
            $table->index('scheduled_at');
        });

        // Deal stages (customizable pipeline stages)
        Schema::create('deal_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#6c757d');
            $table->integer('sort_order')->default(0);
            $table->integer('probability')->default(0); // Default probability for this stage
            $table->boolean('is_won')->default(false);
            $table->boolean('is_lost')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Deals (sales opportunities)
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('deal_number')->unique();
            $table->string('title');
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('customer_id')->nullable(); // Legacy customers table
            $table->foreignId('stage_id')->constrained('deal_stages');

            // Value
            $table->decimal('value', 12, 2);
            $table->string('currency', 3)->default('USD');
            $table->integer('probability')->default(0);

            // Timeline
            $table->date('expected_close_date')->nullable();
            $table->date('actual_close_date')->nullable();

            // Assignment
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->text('notes')->nullable();

            // Products/Services
            $table->json('line_items')->nullable();

            // Tracking
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->string('lost_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['stage_id', 'assigned_to']);
            $table->index('expected_close_date');
        });

        // Deal activities
        Schema::create('deal_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('type', ['call', 'email', 'meeting', 'note', 'task', 'stage_change', 'other']);
            $table->string('subject');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Wholesale accounts
        Schema::create('wholesale_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique();
            $table->unsignedInteger('customer_id'); // Legacy customers table
            $table->index('customer_id');

            // Business info
            $table->string('business_name');
            $table->string('business_type')->nullable(); // Retail, Distributor, etc.
            $table->string('tax_id')->nullable();
            $table->string('resale_certificate')->nullable();

            // Pricing
            $table->enum('tier', ['bronze', 'silver', 'gold', 'platinum'])->default('bronze');
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->integer('payment_terms_days')->default(30);

            // Status
            $table->enum('status', ['pending', 'approved', 'suspended', 'closed'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();

            // Contact
            $table->string('primary_contact_name')->nullable();
            $table->string('primary_contact_email')->nullable();
            $table->string('primary_contact_phone')->nullable();

            // Address
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'tier']);
        });

        // Wholesale orders (for tracking B2B orders)
        Schema::create('wholesale_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('account_id')->constrained('wholesale_accounts')->cascadeOnDelete();
            $table->unsignedInteger('order_id')->nullable(); // Link to legacy orders table

            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);

            $table->enum('status', ['draft', 'pending', 'approved', 'processing', 'shipped', 'delivered', 'cancelled'])->default('draft');
            $table->enum('payment_status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid');
            $table->date('due_date')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'status']);
        });

        // ===========================================
        // ADVANCED ANALYTICS
        // ===========================================

        // Custom reports
        Schema::create('custom_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['sales', 'customers', 'products', 'marketing', 'support', 'custom']);

            // Report configuration
            $table->json('metrics')->nullable(); // What to measure
            $table->json('dimensions')->nullable(); // How to group
            $table->json('filters')->nullable(); // What to filter
            $table->json('date_range')->nullable(); // Time period
            $table->string('chart_type')->default('bar'); // bar, line, pie, table

            // Sharing
            $table->boolean('is_public')->default(false);
            $table->unsignedBigInteger('created_by');
            $table->json('shared_with')->nullable(); // User IDs

            // Scheduling
            $table->boolean('is_scheduled')->default(false);
            $table->string('schedule_frequency')->nullable(); // daily, weekly, monthly
            $table->json('schedule_recipients')->nullable();
            $table->timestamp('last_run_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // Report snapshots (saved report results)
        Schema::create('report_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('custom_reports')->cascadeOnDelete();
            $table->json('data');
            $table->json('summary')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index(['report_id', 'generated_at']);
        });

        // Customer cohorts for cohort analysis
        Schema::create('customer_cohorts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cohort_type'); // acquisition_month, first_purchase_month, etc.
            $table->string('cohort_value'); // 2024-01, etc.
            $table->integer('customer_count')->default(0);
            $table->json('metrics')->nullable(); // Retention rates, LTV, etc.
            $table->date('cohort_date');
            $table->timestamps();

            $table->unique(['cohort_type', 'cohort_value']);
        });

        // Predictive scores
        Schema::create('predictive_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id'); // Legacy customers table
            $table->index('customer_id');

            // Churn prediction
            $table->decimal('churn_probability', 5, 4)->default(0);
            $table->string('churn_risk_level')->nullable(); // low, medium, high
            $table->json('churn_factors')->nullable();

            // Purchase prediction
            $table->decimal('purchase_probability_30d', 5, 4)->default(0);
            $table->decimal('predicted_order_value', 12, 2)->nullable();
            $table->date('predicted_next_order')->nullable();

            // Customer lifetime value
            $table->decimal('predicted_ltv', 12, 2)->default(0);
            $table->decimal('ltv_confidence', 5, 4)->default(0);

            // Engagement score
            $table->integer('engagement_score')->default(0);
            $table->json('engagement_breakdown')->nullable();

            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->index('churn_probability');
            $table->index('engagement_score');
        });

        // ===========================================
        // INTEGRATION HUB
        // ===========================================

        // API keys for external integrations
        Schema::create('api_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('secret'); // Hashed
            $table->text('description')->nullable();

            // Permissions
            $table->json('scopes')->nullable(); // read:customers, write:orders, etc.
            $table->json('ip_whitelist')->nullable();
            $table->integer('rate_limit_per_minute')->default(60);

            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->integer('usage_count')->default(0);
            $table->timestamp('expires_at')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
        });

        // Webhooks
        Schema::create('webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->string('secret')->nullable(); // For signature verification

            // Events to trigger on
            $table->json('events'); // order.created, customer.updated, etc.

            // Configuration
            $table->boolean('is_active')->default(true);
            $table->integer('timeout_seconds')->default(30);
            $table->integer('max_retries')->default(3);

            // Stats
            $table->integer('success_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->text('last_error')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
        });

        // Webhook logs
        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_id')->constrained()->cascadeOnDelete();
            $table->string('event');
            $table->json('payload');
            $table->integer('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->integer('duration_ms')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'retrying'])->default('pending');
            $table->integer('attempt_count')->default(1);
            $table->text('error_message')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();

            $table->index(['webhook_id', 'status']);
            $table->index('created_at');
        });

        // External integrations (Mailchimp, Klaviyo, etc.)
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // mailchimp, klaviyo, quickbooks, etc.
            $table->string('name');
            $table->text('description')->nullable();

            // Credentials (encrypted)
            $table->text('credentials')->nullable();
            $table->json('settings')->nullable();

            // Sync settings
            $table->json('sync_entities')->nullable(); // customers, orders, products
            $table->string('sync_direction')->default('both'); // to, from, both
            $table->integer('sync_interval_minutes')->default(60);

            // Status
            $table->boolean('is_active')->default(false);
            $table->boolean('is_connected')->default(false);
            $table->timestamp('last_sync_at')->nullable();
            $table->text('last_sync_error')->nullable();

            $table->timestamps();

            $table->unique(['provider', 'name']);
        });

        // Integration sync logs
        Schema::create('integration_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained()->cascadeOnDelete();
            $table->string('entity_type'); // customers, orders, etc.
            $table->string('direction'); // to, from
            $table->integer('records_synced')->default(0);
            $table->integer('records_failed')->default(0);
            $table->json('errors')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['running', 'completed', 'failed'])->default('running');
            $table->timestamps();
        });

        // Data exports
        Schema::create('data_exports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['customers', 'orders', 'products', 'analytics', 'custom']);
            $table->json('filters')->nullable();
            $table->json('columns')->nullable();
            $table->enum('format', ['csv', 'xlsx', 'json'])->default('csv');

            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->string('file_path')->nullable();
            $table->integer('file_size')->nullable();
            $table->integer('record_count')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('error_message')->nullable();

            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_exports');
        Schema::dropIfExists('integration_sync_logs');
        Schema::dropIfExists('integrations');
        Schema::dropIfExists('webhook_logs');
        Schema::dropIfExists('webhooks');
        Schema::dropIfExists('api_keys');
        Schema::dropIfExists('predictive_scores');
        Schema::dropIfExists('customer_cohorts');
        Schema::dropIfExists('report_snapshots');
        Schema::dropIfExists('custom_reports');
        Schema::dropIfExists('wholesale_orders');
        Schema::dropIfExists('wholesale_accounts');
        Schema::dropIfExists('deal_activities');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('deal_stages');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('lead_sources');
    }
};
