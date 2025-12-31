<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_returns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('order_item_id');
            $table->uuid('user_id');
            $table->enum('reason', [
                'defective',
                'wrong_item',
                'not_as_described',
                'damaged',
                'size_issue',
                'color_issue',
                'other'
            ]);
            $table->text('description');
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'processing',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->restrictOnDelete();
            $table->foreign('order_item_id')->references('id')->on('order_items')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->index('order_id');
            $table->index('order_item_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};
