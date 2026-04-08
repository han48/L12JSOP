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
            $table->comment('Table of Laravel Framwork, used to manage products.');
            $table->id()->comment('Product ID');
            $table->string('slug', 1024)->nullable()->comment('Slug');
            $table->string('name', 1024)->comment('Name');
            $table->string('image', 2048)->nullable()->comment('Image');
            $table->decimal('price', 20, 2)->default(0)->comment('Price');
            $table->decimal('quantity', 20, 2)->default(-1)->comment('Quantity');
            $table->text('description')->nullable()->comment('Description');
            $table->string('categories', 1024)->nullable()->comment('Categories');
            $table->string('tags', 1024)->nullable()->comment('Tags');
            $table->string('currency')->default('USD')->comment('Currency');
            $table->tinyInteger('status')->default(0)->comment('Status');
            $table->timestamps();
            $table->softDeletes()->comment('Soft Delete');
        });
        try {
            DB::statement('ALTER TABLE products ADD FULLTEXT `search` (`description`, `categories`, `tags`)');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
