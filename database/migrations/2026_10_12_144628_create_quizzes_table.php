<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('course_id')->nullable();
            $table->unsignedBigInteger('module_id')->nullable();
            $table->unsignedBigInteger('chapter_id')->nullable();
            $table->unsignedBigInteger('lesson_id')->nullable();

            $table->string('title');
            $table->text('description')->nullable();

            $table->enum('type', [
                'quiz',
                'pre_test',
                'post_test',
                'exam',
            ])->default('quiz');

            $table->integer('passing_score')->default(70);
            $table->integer('max_attempts')->default(3);
            $table->integer('time_limit_minutes')->nullable();
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('show_results_immediately')->default(true);
            $table->boolean('is_published')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')->on('courses')
                ->cascadeOnDelete();

            $table->foreign('module_id')
                ->references('id')->on('modules')
                ->cascadeOnDelete();

            $table->foreign('chapter_id')
                ->references('id')->on('chapters')
                ->cascadeOnDelete();

            $table->foreign('lesson_id')
                ->references('id')->on('lessons')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};