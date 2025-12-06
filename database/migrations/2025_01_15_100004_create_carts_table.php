<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('currency', 3)->default('XOF');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index('user_id');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};





