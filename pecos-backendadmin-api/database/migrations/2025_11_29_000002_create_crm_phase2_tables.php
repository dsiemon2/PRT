<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CRM Phase 2: Support Tickets, Marketing Automation, Enhanced Loyalty
     */
    public function up(): void
    {
        // =====================
        // SUPPORT TICKET SYSTEM
        // =====================

        // Support Tickets
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number', 20)->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('subject');
            $table->enum('category', ['order', 'return', 'product', 'shipping', 'billing', 'other'])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'pending_customer', 'resolved', 'closed'])->default('open');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->tinyInteger('satisfaction_rating')->nullable();
            $table->text('satisfaction_comment')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('status');
            $table->index('priority');
            $table->index('assigned_to');
        });

        // Ticket Messages
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->enum('sender_type', ['customer', 'staff']);
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->boolean('is_internal')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('support_tickets')->onDelete('cascade');
            $table->index('ticket_id');
        });

        // Canned Responses for Tickets
        Schema::create('canned_responses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('shortcut', 50)->nullable();
            $table->text('content');
            $table->string('category', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // =====================
        // MARKETING AUTOMATION
        // =====================

        // Automation Workflows
        Schema::create('automation_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->enum('trigger_type', ['time', 'behavior', 'threshold', 'event', 'manual']);
            $table->json('trigger_config');
            $table->boolean('is_active')->default(false);
            $table->json('stats')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // Automation Steps
        Schema::create('automation_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id');
            $table->integer('step_order');
            $table->enum('step_type', ['email', 'sms', 'wait', 'condition', 'action', 'split']);
            $table->json('config');
            $table->timestamps();

            $table->foreign('workflow_id')->references('id')->on('automation_workflows')->onDelete('cascade');
        });

        // Automation Enrollments
        Schema::create('automation_enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('workflow_id');
            $table->unsignedBigInteger('customer_id');
            $table->integer('current_step')->nullable();
            $table->enum('status', ['active', 'completed', 'exited', 'paused'])->default('active');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->foreign('workflow_id')->references('id')->on('automation_workflows')->onDelete('cascade');
            $table->index(['workflow_id', 'customer_id']);
            $table->index('status');
        });

        // Automation Logs (for tracking sent emails, etc.)
        Schema::create('automation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('step_id');
            $table->string('action', 100);
            $table->enum('status', ['success', 'failed', 'skipped']);
            $table->text('details')->nullable();
            $table->timestamps();

            $table->foreign('enrollment_id')->references('id')->on('automation_enrollments')->onDelete('cascade');
            $table->index('enrollment_id');
        });

        // Abandoned Carts Tracking
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('email')->nullable();
            $table->json('cart_items');
            $table->decimal('cart_total', 10, 2);
            $table->integer('item_count');
            $table->boolean('is_recovered')->default(false);
            $table->unsignedBigInteger('recovered_order_id')->nullable();
            $table->integer('reminder_count')->default(0);
            $table->timestamp('last_reminder_at')->nullable();
            $table->timestamp('abandoned_at')->useCurrent();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('email');
            $table->index('is_recovered');
            $table->index('abandoned_at');
        });

        // =====================
        // ENHANCED LOYALTY
        // =====================

        // Referrals
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('referrer_id');
            $table->string('referee_email');
            $table->unsignedBigInteger('referee_id')->nullable();
            $table->string('referral_code', 20);
            $table->enum('status', ['pending', 'signed_up', 'first_purchase', 'credited'])->default('pending');
            $table->decimal('referrer_credit', 10, 2)->default(0);
            $table->decimal('referee_credit', 10, 2)->default(0);
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->index('referrer_id');
            $table->index('referral_code');
            $table->index('referee_email');
        });

        // Loyalty Achievements/Badges
        Schema::create('loyalty_achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('badge_icon')->nullable();
            $table->json('criteria');
            $table->integer('points_reward')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Customer Achievements (junction table)
        Schema::create('customer_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('achievement_id');
            $table->timestamp('earned_at')->useCurrent();

            $table->unique(['customer_id', 'achievement_id']);
            $table->foreign('achievement_id')->references('id')->on('loyalty_achievements')->onDelete('cascade');
        });

        // Loyalty Point Actions (for bonus points on actions)
        Schema::create('loyalty_point_rules', function (Blueprint $table) {
            $table->id();
            $table->string('action_type', 50); // purchase, review, referral, birthday, etc.
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->integer('points_awarded');
            $table->enum('points_type', ['fixed', 'multiplier'])->default('fixed');
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // =====================
        // TASK MANAGEMENT
        // =====================

        // Tasks
        Schema::create('admin_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('task_type', ['call', 'email', 'follow_up', 'review', 'meeting', 'other'])->default('other');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->dateTime('due_date')->nullable();
            $table->dateTime('reminder_at')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->string('related_type', 50)->nullable(); // customer, order, lead, ticket
            $table->unsignedBigInteger('related_id')->nullable();
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_rule', 100)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('assigned_to');
            $table->index('status');
            $table->index('due_date');
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_tasks');
        Schema::dropIfExists('loyalty_point_rules');
        Schema::dropIfExists('customer_achievements');
        Schema::dropIfExists('loyalty_achievements');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('abandoned_carts');
        Schema::dropIfExists('automation_logs');
        Schema::dropIfExists('automation_enrollments');
        Schema::dropIfExists('automation_steps');
        Schema::dropIfExists('automation_workflows');
        Schema::dropIfExists('canned_responses');
        Schema::dropIfExists('ticket_messages');
        Schema::dropIfExists('support_tickets');
    }
};
