<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('enrollment_id')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->decimal('score', 5, 2)->default(0);
            $table->decimal('max_score', 5, 2)->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->boolean('passed')->default(false);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('submitted_at')->nullable();
            $table->integer('time_spent_seconds')->default(0);

            $table->enum('status', [
                'in_progress',
                'submitted',
                'graded',
                'abandoned',
            ])->default('in_progress');

            $table->timestamps();

            $table->foreign('quiz_id')
                ->references('id')->on('quizzes')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('enrollment_id')
                ->references('id')->on('enrollments')
                ->cascadeOnDelete();

            $table->index(
                ['user_id', 'quiz_id'],
                'idx_user_quiz'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};