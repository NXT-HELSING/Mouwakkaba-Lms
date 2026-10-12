<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->integer('duration_minutes')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->foreign('module_id')
                ->references('id')->on('modules')
                ->cascadeOnDelete();

            $table->index(
                ['module_id', 'order'],
                'idx_module_order'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};