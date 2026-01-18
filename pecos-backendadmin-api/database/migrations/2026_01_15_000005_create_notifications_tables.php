<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SMS Templates
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('content');
            $table->string('event_trigger')->nullable(); // order_placed, order_shipped, password_reset, etc.
            $table->json('variables')->nullable(); // Available merge variables
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Push Notification Templates
        Schema::create('push_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('title');
            $table->text('body');
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->string('event_trigger')->nullable();
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Notification Channels (configuration)
        Schema::create('notification_channels', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // sms, push, email
            $table->string('provider'); // twilio, aws_sns, firebase, onesignal
            $table->string('name');
            $table->json('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // SMS Messages Log
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->nullable()->index(); // References customers.CustomerID
            $table->foreignId('template_id')->nullable()->constrained('sms_templates')->nullOnDelete();
            $table->string('phone_number');
            $table->text('content');
            $table->string('provider')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'bounced'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('segments')->default(1);
            $table->decimal('cost', 10, 4)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index('phone_number');
            $table->index('provider_message_id');
        });

        // Push Notifications Log
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->nullable()->index(); // References customers.CustomerID
            $table->foreignId('template_id')->nullable()->constrained('push_templates')->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->json('data')->nullable(); // Additional payload data
            $table->string('provider')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'clicked', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
        });

        // Customer Device Tokens (for push notifications)
        Schema::create('customer_device_tokens', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->index(); // References customers.CustomerID
            $table->string('token');
            $table->enum('platform', ['ios', 'android', 'web']);
            $table->string('device_name')->nullable();
            $table->string('device_model')->nullable();
            $table->string('os_version')->nullable();
            $table->string('app_version')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['customer_id', 'token']);
            $table->index('token');
        });

        // Customer Notification Preferences
        Schema::create('customer_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id')->unique(); // References customers.CustomerID
            $table->boolean('sms_enabled')->default(true);
            $table->boolean('push_enabled')->default(true);
            $table->boolean('email_enabled')->default(true);
            $table->boolean('marketing_sms')->default(false);
            $table->boolean('marketing_push')->default(false);
            $table->boolean('marketing_email')->default(true);
            $table->boolean('order_updates_sms')->default(true);
            $table->boolean('order_updates_push')->default(true);
            $table->boolean('order_updates_email')->default(true);
            $table->boolean('promotions_sms')->default(false);
            $table->boolean('promotions_push')->default(true);
            $table->boolean('promotions_email')->default(true);
            $table->boolean('price_alerts_sms')->default(false);
            $table->boolean('price_alerts_push')->default(true);
            $table->boolean('price_alerts_email')->default(true);
            $table->boolean('back_in_stock_sms')->default(false);
            $table->boolean('back_in_stock_push')->default(true);
            $table->boolean('back_in_stock_email')->default(true);
            $table->string('quiet_hours_start')->nullable(); // e.g., "22:00"
            $table->string('quiet_hours_end')->nullable(); // e.g., "08:00"
            $table->string('timezone')->default('UTC');
            $table->timestamps();
        });

        // Notification Campaigns
        Schema::create('notification_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['sms', 'push', 'both']);
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'paused', 'cancelled'])->default('draft');
            $table->foreignId('sms_template_id')->nullable()->constrained('sms_templates')->nullOnDelete();
            $table->foreignId('push_template_id')->nullable()->constrained('push_templates')->nullOnDelete();
            $table->json('audience_filters')->nullable(); // Targeting criteria
            $table->integer('audience_count')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('sms_sent')->default(0);
            $table->integer('sms_delivered')->default(0);
            $table->integer('sms_failed')->default(0);
            $table->integer('push_sent')->default(0);
            $table->integer('push_delivered')->default(0);
            $table->integer('push_clicked')->default(0);
            $table->integer('push_failed')->default(0);
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->unsignedInteger('created_by')->nullable(); // References users
            $table->timestamps();

            $table->index('status');
            $table->index('scheduled_at');
        });

        // Notification Campaign Recipients
        Schema::create('notification_campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('notification_campaigns')->cascadeOnDelete();
            $table->unsignedInteger('customer_id')->index(); // References customers.CustomerID
            $table->foreignId('sms_message_id')->nullable()->constrained('sms_messages')->nullOnDelete();
            $table->foreignId('push_notification_id')->nullable()->constrained('push_notifications')->nullOnDelete();
            $table->timestamps();

            $table->unique(['campaign_id', 'customer_id']);
        });

        // Notification Automations (triggered notifications)
        Schema::create('notification_automations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('trigger_event'); // order_placed, order_shipped, abandoned_cart, etc.
            $table->json('trigger_conditions')->nullable();
            $table->integer('delay_minutes')->default(0); // Delay before sending
            $table->enum('notification_type', ['sms', 'push', 'both']);
            $table->foreignId('sms_template_id')->nullable()->constrained('sms_templates')->nullOnDelete();
            $table->foreignId('push_template_id')->nullable()->constrained('push_templates')->nullOnDelete();
            $table->boolean('is_active')->default(false);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->timestamps();

            $table->index(['trigger_event', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_automations');
        Schema::dropIfExists('notification_campaign_recipients');
        Schema::dropIfExists('notification_campaigns');
        Schema::dropIfExists('customer_notification_preferences');
        Schema::dropIfExists('customer_device_tokens');
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('sms_messages');
        Schema::dropIfExists('notification_channels');
        Schema::dropIfExists('push_templates');
        Schema::dropIfExists('sms_templates');
    }
};
