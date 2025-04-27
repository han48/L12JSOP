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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id');
            $table->string('title');
            $table->string('image', 2048);
            $table->text('html');
            $table->string('categories')->nullable();
            $table->string('tags')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id');
            $table->foreignId('post_id');
            $table->text('content');
            $table->tinyInteger('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('viewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id');
            $table->foreignId('post_id');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('viewer');
    }
};
