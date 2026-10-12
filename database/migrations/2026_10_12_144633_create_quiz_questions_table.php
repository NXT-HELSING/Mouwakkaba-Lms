<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quiz_id');
            $table->text('question');

            $table->enum('type', [
                'single',
                'multiple',
                'true_false',
                'text',
            ])->default('single');

            $table->text('explanation')->nullable();
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('quiz_id')
                ->references('id')->on('quizzes')
                ->cascadeOnDelete();

            $table->index(
                ['quiz_id', 'order'],
                'idx_quiz_order'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};