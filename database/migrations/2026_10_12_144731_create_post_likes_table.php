<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('likeable_type');
            $table->unsignedBigInteger('likeable_id');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(
                ['user_id', 'likeable_type', 'likeable_id'],
                'unique_like'
            );

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_likes');
    }
};