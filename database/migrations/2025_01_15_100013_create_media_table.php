<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('model_type');
            $table->uuid('model_id');
            $table->string('collection_name')->default('default');
            $table->string('file_name');
            $table->string('mime_type');
            $table->string('disk')->default('public');
            $table->unsignedBigInteger('size');
            $table->json('custom_properties')->nullable();
            $table->integer('order_column')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('collection_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};













