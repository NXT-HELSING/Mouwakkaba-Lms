<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('enrollment_id');
            $table->string('certificate_number', 50)->unique();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->string('pdf_path', 500)->nullable();
            $table->string('template_used')->nullable();
            $table->string('verification_code', 50)->nullable()->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('course_id')
                ->references('id')->on('courses')
                ->cascadeOnDelete();

            $table->foreign('enrollment_id')
                ->references('id')->on('enrollments')
                ->cascadeOnDelete();

            $table->index('user_id', 'idx_user');
            $table->index('course_id', 'idx_course');
            $table->index(
                'certificate_number',
                'idx_certificate_number'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};