<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->text('objectives')->nullable();
            $table->text('prerequisites')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('promo_video')->nullable();

            $table->enum('level', [
                'beginner',
                'intermediate',
                'advanced',
            ])->default('beginner');

            $table->integer('duration_minutes')->default(0);
            $table->string('language', 10)->default('fr');
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('is_free')->default(true);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('max_students')->nullable();
            $table->boolean('certificate_enabled')->default(true);
            $table->string('certificate_template')->nullable();
            $table->integer('passing_score')->default(70);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('instructor_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->nullOnDelete();

            $table->foreign('instructor_id')
                ->references('id')->on('users')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')->on('users');

            $table->index(
                ['is_published', 'published_at'],
                'idx_published'
            );

            $table->index('instructor_id', 'idx_instructor');

            $table->fullText(
                ['title', 'subtitle', 'description'],
                'idx_search'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};