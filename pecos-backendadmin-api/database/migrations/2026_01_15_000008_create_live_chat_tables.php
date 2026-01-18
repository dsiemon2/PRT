<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Chat Agents (admin users who can handle chats)
        Schema::create('chat_agents', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->index(); // References users
            $table->string('display_name');
            $table->string('avatar_url')->nullable();
            $table->string('status')->default('offline'); // online, offline, away, busy
            $table->integer('max_concurrent_chats')->default(5);
            $table->integer('current_chat_count')->default(0);
            $table->json('skills')->nullable(); // Technical, Sales, Billing, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
        });

        // Chat Sessions
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->unsignedInteger('customer_id')->nullable()->index(); // References customers.CustomerID
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->foreignId('agent_id')->nullable()->constrained('chat_agents')->nullOnDelete();
            $table->string('status')->default('waiting'); // waiting, active, closed, transferred
            $table->string('channel')->default('website'); // website, mobile_app, facebook, instagram
            $table->string('department')->nullable(); // sales, support, billing
            $table->string('subject')->nullable();
            $table->integer('priority')->default(1); // 1=low, 2=medium, 3=high
            $table->integer('wait_time_seconds')->default(0);
            $table->text('initial_message')->nullable();
            $table->string('visitor_ip')->nullable();
            $table->string('visitor_user_agent')->nullable();
            $table->string('visitor_page_url')->nullable();
            $table->json('visitor_metadata')->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('agent_id');
        });

        // Chat Messages
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('chat_sessions')->cascadeOnDelete();
            $table->string('sender_type'); // visitor, agent, system
            $table->foreignId('agent_id')->nullable()->constrained('chat_agents')->nullOnDelete();
            $table->text('message');
            $table->string('message_type')->default('text'); // text, image, file, link, card
            $table->json('attachments')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['session_id', 'created_at']);
        });

        // Canned Responses for Chat
        Schema::create('chat_canned_responses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('shortcut')->unique();
            $table->text('content');
            $table->string('category')->nullable();
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Chat Departments
        Schema::create('chat_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('email')->nullable(); // Offline message notification email
            $table->boolean('is_active')->default(true);
            $table->json('working_hours')->nullable(); // Per-day schedule
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Department Agents (many-to-many)
        Schema::create('chat_department_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('chat_departments')->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('chat_agents')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['department_id', 'agent_id']);
        });

        // Chat Triggers (proactive chat rules)
        Schema::create('chat_triggers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('trigger_type'); // page_time, scroll_depth, exit_intent, page_url, cart_value
            $table->json('conditions'); // Trigger conditions
            $table->text('message');
            $table->string('department_code')->nullable();
            $table->integer('delay_seconds')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('triggered_count')->default(0);
            $table->integer('accepted_count')->default(0);
            $table->timestamps();
        });

        // Offline Messages
        Schema::create('chat_offline_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('department_code')->nullable();
            $table->text('message');
            $table->string('status')->default('new'); // new, read, replied, closed
            $table->foreignId('assigned_to')->nullable()->constrained('chat_agents')->nullOnDelete();
            $table->text('reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();
        });

        // Chat Widget Settings
        Schema::create('chat_widget_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Chat Analytics (daily aggregates)
        Schema::create('chat_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('total_chats')->default(0);
            $table->integer('missed_chats')->default(0);
            $table->integer('answered_chats')->default(0);
            $table->integer('avg_wait_time')->default(0); // seconds
            $table->integer('avg_chat_duration')->default(0); // seconds
            $table->decimal('avg_rating', 2, 1)->nullable();
            $table->integer('total_messages')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->timestamps();

            $table->unique('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_analytics');
        Schema::dropIfExists('chat_widget_settings');
        Schema::dropIfExists('chat_offline_messages');
        Schema::dropIfExists('chat_triggers');
        Schema::dropIfExists('chat_department_agents');
        Schema::dropIfExists('chat_departments');
        Schema::dropIfExists('chat_canned_responses');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('chat_agents');
    }
};
