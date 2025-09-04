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
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('code', 512)->unique();
            $table->text('data');
            $table->string('image', 2048)->nullable();
            $table->timestamp('issue_date')->nullable();
            $table->timestamp('payment_date')->nullable();
            $table->decimal('amount', 20, 2)->default(0);
            $table->decimal('tax', 20, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->tinyInteger('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->nullable();
            $table->foreignId('product_id')->nullable();
            $table->decimal('price', 20, 2)->default(0);
            $table->decimal('quantity', 20, 2)->default(0);
            $table->string('currency')->default('USD');
            $table->tinyInteger('status')->default(0);
            $table->softDeletes();
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
