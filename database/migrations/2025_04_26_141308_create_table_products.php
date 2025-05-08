<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 1024)->nullable();
            $table->string('name', 1024);
            $table->string('image', 2048)->nullable();
            $table->decimal('price', 20, 2)->default(0);
            $table->decimal('quantity', 20, 2)->default(-1);
            $table->text('description')->nullable();
            $table->string('categories', 1024)->nullable();
            $table->string('tags', 1024)->nullable();
            $table->string('currency')->default('USD');
            $table->tinyInteger('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
        DB::statement('ALTER TABLE products ADD FULLTEXT `search` (`description`, `categories`, `tags`)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
