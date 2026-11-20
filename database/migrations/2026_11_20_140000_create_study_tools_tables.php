<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('priority', 1)->default('B');
            $table->string('status', 16)->default('todo');
            $table->date('due_on')->nullable();
            $table->unsignedSmallInteger('estimate_minutes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_on']);
        });

        Schema::create('subtasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->boolean('done')->default(false);
            $table->timestamps();
        });

        Schema::create('deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('kind', 16);
            $table->dateTime('happens_at');
            $table->string('location')->nullable();
            $table->decimal('coefficient', 5, 2)->nullable();
            $table->decimal('grade', 5, 2)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'happens_at']);
        });

        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['user_id', 'name']);
        });

        Schema::create('habit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('logged_on');
            $table->timestamps();

            $table->unique(['habit_id', 'logged_on']);
        });

        Schema::create('study_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->unsignedSmallInteger('minutes')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'started_at']);
        });

        Schema::create('revision_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('stage', 16)->default('reading');
            $table->unsignedTinyInteger('confidence')->default(1);
            $table->timestamps();
        });

        Schema::create('weekly_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->text('wins')->nullable();
            $table->text('hard')->nullable();
            $table->text('next_step')->nullable();
            $table->unsignedTinyInteger('satisfaction')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'week_start']);
        });

        Schema::create('shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->timestamps();
        });

        Schema::create('reminder_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject_key');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['user_id', 'subject_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_sends');
        Schema::dropIfExists('shares');
        Schema::dropIfExists('weekly_reviews');
        Schema::dropIfExists('revision_items');
        Schema::dropIfExists('study_sessions');
        Schema::dropIfExists('habit_logs');
        Schema::dropIfExists('habits');
        Schema::dropIfExists('deadlines');
        Schema::dropIfExists('subtasks');
        Schema::dropIfExists('tasks');
    }
};
