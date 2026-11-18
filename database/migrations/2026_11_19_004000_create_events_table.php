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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('timezone', 64);
            $table->boolean('is_all_day')->default(false);
            $table->string('recurrence_frequency')->nullable();
            $table->unsignedTinyInteger('recurrence_interval')->default(1);
            $table->date('recurrence_until')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'starts_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
