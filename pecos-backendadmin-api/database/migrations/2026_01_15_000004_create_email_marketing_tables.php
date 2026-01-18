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
        // Email Lists/Audiences
        Schema::create('email_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('double_optin')->default(false);
            $table->string('welcome_email_template')->nullable();
            $table->timestamps();
        });

        // Email Subscribers
        Schema::create('email_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_list_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->unsignedInteger('customer_id')->nullable()->index(); // References customers.CustomerID
            $table->string('status', 20)->default('subscribed'); // subscribed, unsubscribed, pending, bounced
            $table->string('source', 50)->nullable(); // website, import, api, checkout
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('unsubscribe_reason')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['email_list_id', 'email']);
            $table->index('status');
        });

        // Email Campaigns
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subject');
            $table->string('preview_text')->nullable();
            $table->string('from_name');
            $table->string('from_email');
            $table->string('reply_to')->nullable();
            $table->string('type', 30)->default('regular'); // regular, automated, ab_test
            $table->string('status', 20)->default('draft'); // draft, scheduled, sending, sent, paused, cancelled
            $table->text('html_content')->nullable();
            $table->text('text_content')->nullable();
            $table->unsignedBigInteger('template_id')->nullable()->index(); // References email_templates
            $table->foreignId('email_list_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('segment_id')->nullable()->index(); // References crm_segments
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->integer('bounce_count')->default(0);
            $table->integer('unsubscribe_count')->default(0);
            $table->integer('spam_count')->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('type');
        });

        // Campaign Recipients (tracking individual sends)
        Schema::create('campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('email_campaigns')->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained('email_subscribers')->onDelete('cascade');
            $table->string('email');
            $table->string('status', 20)->default('pending'); // pending, sent, failed, bounced
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->string('failure_reason')->nullable();
            $table->timestamps();

            $table->unique(['campaign_id', 'subscriber_id']);
            $table->index('status');
        });

        // Campaign Links (for click tracking)
        Schema::create('campaign_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('email_campaigns')->onDelete('cascade');
            $table->text('original_url');
            $table->string('tracking_url')->unique();
            $table->integer('click_count')->default(0);
            $table->timestamps();
        });

        // Link Clicks
        Schema::create('campaign_link_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained('campaign_links')->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained('campaign_recipients')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('clicked_at');
            $table->timestamps();
        });

        // Automated Email Sequences (Drip Campaigns)
        Schema::create('email_automations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger_type', 50); // signup, purchase, abandoned_cart, birthday, custom
            $table->json('trigger_conditions')->nullable();
            $table->foreignId('email_list_id')->nullable()->constrained()->onDelete('set null');
            $table->boolean('is_active')->default(false);
            $table->integer('total_subscribers')->default(0);
            $table->integer('total_completed')->default(0);
            $table->timestamps();
        });

        // Automation Steps (individual emails in sequence)
        Schema::create('automation_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained('email_automations')->onDelete('cascade');
            $table->integer('step_order');
            $table->string('name');
            $table->string('subject');
            $table->text('html_content')->nullable();
            $table->text('text_content')->nullable();
            $table->unsignedBigInteger('template_id')->nullable()->index(); // References email_templates
            $table->integer('delay_value')->default(0);
            $table->string('delay_unit', 10)->default('hours'); // minutes, hours, days, weeks
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sent_count')->default(0);
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->timestamps();

            $table->unique(['automation_id', 'step_order']);
        });

        // Automation Subscribers (tracking progress through automation)
        Schema::create('automation_subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained('email_automations')->onDelete('cascade');
            $table->foreignId('subscriber_id')->constrained('email_subscribers')->onDelete('cascade');
            $table->foreignId('current_step_id')->nullable()->constrained('automation_steps')->onDelete('set null');
            $table->string('status', 20)->default('active'); // active, completed, paused, unsubscribed
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('next_email_at')->nullable();
            $table->timestamps();

            $table->unique(['automation_id', 'subscriber_id']);
        });

        // A/B Test Variants
        Schema::create('campaign_ab_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('email_campaigns')->onDelete('cascade');
            $table->string('variant_name', 10); // A, B, C, etc.
            $table->string('subject')->nullable();
            $table->text('html_content')->nullable();
            $table->string('from_name')->nullable();
            $table->integer('percentage')->default(50);
            $table->boolean('is_winner')->default(false);
            $table->integer('sent_count')->default(0);
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_ab_variants');
        Schema::dropIfExists('automation_subscribers');
        Schema::dropIfExists('automation_steps');
        Schema::dropIfExists('email_automations');
        Schema::dropIfExists('campaign_link_clicks');
        Schema::dropIfExists('campaign_links');
        Schema::dropIfExists('campaign_recipients');
        Schema::dropIfExists('email_campaigns');
        Schema::dropIfExists('email_subscribers');
        Schema::dropIfExists('email_lists');
    }
};
