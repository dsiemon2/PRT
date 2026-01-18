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
        // Product Questions
        Schema::create('product_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('question');
            $table->enum('status', ['pending', 'approved', 'rejected', 'answered'])->default('pending');
            $table->integer('helpful_votes')->default(0);
            $table->integer('unhelpful_votes')->default(0);
            $table->timestamps();

            $table->index('product_id');
            $table->index('status');
        });

        // Product Answers
        Schema::create('product_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('user_id')->nullable(); // Admin/staff who answered
            $table->string('answered_by')->nullable(); // Name of person/brand
            $table->enum('answer_type', ['official', 'customer'])->default('official');
            $table->text('answer');
            $table->boolean('is_verified')->default(false);
            $table->integer('helpful_votes')->default(0);
            $table->integer('unhelpful_votes')->default(0);
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('product_questions')->onDelete('cascade');
            $table->index('question_id');
        });

        // Q&A Votes tracking (to prevent duplicate votes)
        Schema::create('qa_votes', function (Blueprint $table) {
            $table->id();
            $table->enum('vote_type', ['question', 'answer']);
            $table->unsignedBigInteger('item_id'); // question_id or answer_id
            $table->string('voter_ip')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->enum('vote', ['helpful', 'unhelpful']);
            $table->timestamps();

            $table->unique(['vote_type', 'item_id', 'voter_ip']);
            $table->index(['vote_type', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qa_votes');
        Schema::dropIfExists('product_answers');
        Schema::dropIfExists('product_questions');
    }
};
