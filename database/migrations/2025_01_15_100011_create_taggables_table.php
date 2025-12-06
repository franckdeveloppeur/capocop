<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taggables', function (Blueprint $table) {
            $table->uuid('tag_id');
            $table->uuid('taggable_id');
            $table->string('taggable_type');

            $table->foreign('tag_id')->references('id')->on('tags')->cascadeOnDelete();
            $table->index(['taggable_id', 'taggable_type']);
            $table->index('tag_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taggables');
    }
};





