<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->enum('type', [
                'pdf',
                'doc',
                'video',
                'audio',
                'image',
                'link',
                'zip',
            ]);

            $table->string('file_path', 500)->nullable();
            $table->string('external_url', 500)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('thumbnail')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->json('tags')->nullable();
            $table->string('language', 10)->default('fr');
            $table->boolean('is_public')->default(true);
            $table->boolean('is_downloadable')->default(true);
            $table->integer('download_count')->default(0);
            $table->integer('view_count')->default(0);

            $table->enum('status', [
                'draft',
                'published',
                'archived',
            ])->default('published');

            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')->on('users');

            $table->fullText(
                ['title', 'description'],
                'idx_search'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};