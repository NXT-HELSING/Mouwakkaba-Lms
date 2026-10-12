<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->boolean('is_published')->default(true);
            $table->unsignedBigInteger('unlock_after_module_id')->nullable();
            $table->timestamps();

            $table->foreign('course_id')
                ->references('id')->on('courses')
                ->cascadeOnDelete();

            $table->foreign('unlock_after_module_id')
                ->references('id')->on('modules')
                ->nullOnDelete();

            $table->index(
                ['course_id', 'order'],
                'idx_course_order'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};