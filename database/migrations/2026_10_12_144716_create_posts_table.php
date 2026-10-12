<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('title')->nullable();
            $table->longText('content');

            $table->enum('type', [
                'text',
                'image',
                'video',
                'link',
                'poll',
            ])->default('text');

            $table->json('attachments')->nullable();

            $table->enum('visibility', [
                'public',
                'members',
                'organization',
            ])->default('public');

            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->nullOnDelete();

            $table->index(
                ['is_published', 'published_at'],
                'idx_published'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};