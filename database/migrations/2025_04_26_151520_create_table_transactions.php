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

        Schema::create('transactions', function (Blueprint $table) {
            $table->id()->comment('Transaction ID');
            $table->foreignId('user_id')->nullable()->comment('User ID');
            $table->string('code', 512)->unique()->comment('Code');
            $table->text('data')->comment('Data');
            $table->string('image', 2048)->nullable()->comment('Image');
            $table->timestamp('issue_date')->nullable()->comment('Issue Date');
            $table->timestamp('payment_date')->nullable()->comment('Payment Date');
            $table->decimal('amount', 20, 2)->default(0)->comment('Amount');
            $table->decimal('tax', 20, 2)->default(0)->comment('Tax');
            $table->string('currency')->default('USD')->comment('Currency');
            $table->tinyInteger('status')->default(0)->comment('Status');
            $table->softDeletes()->comment('Soft Delete');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id()->comment('Order Item ID');
            $table->foreignId('transaction_id')->nullable()->comment('Transaction ID');
            $table->foreignId('product_id')->nullable()->comment('Product ID');
            $table->decimal('price', 20, 2)->default(0)->comment('Price');
            $table->decimal('quantity', 20, 2)->default(0)->comment('Quantity');
            $table->string('currency')->default('USD')->comment('Currency');
            $table->tinyInteger('status')->default(0)->comment('Status');
            $table->softDeletes()->comment('Soft Delete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('order_items');
    }
};
