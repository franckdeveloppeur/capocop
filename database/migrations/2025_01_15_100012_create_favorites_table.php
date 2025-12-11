<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('favoritable_type');
            $table->uuid('favoritable_id');
            $table->timestamp('created_at');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['favoritable_id', 'favoritable_type']);
            $table->index('user_id');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};













