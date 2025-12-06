<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installment_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('deposit_amount', 15, 2);
            $table->integer('number_of_installments');
            $table->integer('interval_days');
            $table->enum('status', ['active', 'completed', 'defaulted', 'cancelled'])->default('active');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->restrictOnDelete();
            $table->unique('order_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installment_plans');
    }
};





