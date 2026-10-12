<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enrollment_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('lesson_id');

            $table->enum('status', [
                'not_started',
                'in_progress',
                'completed',
            ])->default('not_started');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);
            $table->integer('last_position')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(
                ['enrollment_id', 'lesson_id'],
                'unique_enrollment_lesson'
            );

            $table->foreign('enrollment_id')
                ->references('id')->on('enrollments')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('lesson_id')
                ->references('id')->on('lessons')
                ->cascadeOnDelete();

            $table->index('status', 'idx_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};