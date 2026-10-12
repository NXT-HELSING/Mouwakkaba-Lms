<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attempt_id');
            $table->unsignedBigInteger('question_id');
            $table->unsignedBigInteger('answer_id')->nullable();
            $table->text('text_answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('points_earned', 5, 2)->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('attempt_id')
                ->references('id')->on('quiz_attempts')
                ->cascadeOnDelete();

            $table->foreign('question_id')
                ->references('id')->on('quiz_questions')
                ->cascadeOnDelete();

            $table->foreign('answer_id')
                ->references('id')->on('quiz_answers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_responses');
    }
};