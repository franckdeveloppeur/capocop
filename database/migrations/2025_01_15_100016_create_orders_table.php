<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->uuid('shop_id')->nullable();
            $table->uuid('address_id');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('shipping_amount', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->enum('status', ['pending', 'processing', 'paid', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_method', ['mobile_money', 'card', 'wallet', 'installment'])->nullable();
            $table->boolean('is_installment')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('shop_id')->references('id')->on('shops')->nullOnDelete();
            $table->foreign('address_id')->references('id')->on('addresses')->restrictOnDelete();
            $table->index('user_id');
            $table->index('shop_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};





