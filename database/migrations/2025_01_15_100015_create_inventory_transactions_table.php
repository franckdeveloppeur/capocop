<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('variant_id');
            $table->integer('change'); // positive for restock, negative for sale
            $table->enum('reason', ['sale', 'restock', 'adjustment', 'return']);
            $table->json('meta')->nullable();
            $table->timestamp('created_at');

            $table->foreign('variant_id')->references('id')->on('product_variants')->restrictOnDelete();
            $table->index('variant_id');
            $table->index('reason');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};













