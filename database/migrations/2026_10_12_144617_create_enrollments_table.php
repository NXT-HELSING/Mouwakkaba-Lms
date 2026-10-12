<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->unsignedBigInteger('last_lesson_id')->nullable();

            $table->timestamp('last_activity_at')->nullable();

            $table->enum('status', [
                'active',
                'completed',
                'dropped',
                'expired',
            ])->default('active');

            $table->unsignedBigInteger(
                'completion_certificate_id'
            )->nullable();

            $table->timestamps();

            $table->unique(
                ['user_id', 'course_id'],
                'unique_user_course'
            );

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('course_id')
                ->references('id')->on('courses')
                ->cascadeOnDelete();

            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->nullOnDelete();

            $table->index(
                ['user_id', 'status'],
                'idx_user_status'
            );

            $table->index(
                ['course_id', 'status'],
                'idx_course_status'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};