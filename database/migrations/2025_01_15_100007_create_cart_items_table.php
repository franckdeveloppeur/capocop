<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cart_id');
            $table->uuid('product_id');
            $table->uuid('variant_id')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->timestamps();

            $table->foreign('cart_id')->references('id')->on('carts')->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
            $table->foreign('variant_id')->references('id')->on('product_variants')->nullOnDelete();
            $table->index('cart_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};






