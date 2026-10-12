<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chapter_id');
            $table->string('title');
            $table->string('slug')->nullable();

            $table->enum('type', [
                'video',
                'text',
                'pdf',
                'audio',
                'slides',
                'quiz',
                'live',
            ]);

            $table->longText('content')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->integer('video_duration')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('external_url', 500)->nullable();
            $table->integer('order')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->boolean('is_published')->default(true);
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->foreign('chapter_id')
                ->references('id')->on('chapters')
                ->cascadeOnDelete();

            $table->index(
                ['chapter_id', 'order'],
                'idx_chapter_order'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};