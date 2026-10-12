<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('expertise', 100)->nullable();
            $table->date('date_of_birth')->nullable();

            $table->enum('gender', ['M', 'F', 'other'])
                ->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'suspended',
            ])->default('active');

            $table->unsignedBigInteger('organization_id')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->softDeletes();

            $table->index('email', 'idx_email');
            $table->index('organization_id', 'idx_organization');
            $table->index('status', 'idx_status');

            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);

            $table->dropIndex('idx_email');
            $table->dropIndex('idx_organization');
            $table->dropIndex('idx_status');

            $table->dropColumn([
                'phone',
                'avatar',
                'bio',
                'city',
                'expertise',
                'date_of_birth',
                'gender',
                'status',
                'organization_id',
                'last_login_at',
                'deleted_at',
            ]);
        });
    }
};