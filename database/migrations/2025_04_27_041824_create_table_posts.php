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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id');
            $table->string('slug')->unique()->nullable();
            $table->string('title');
            $table->string('image', 2048)->nullable();
            $table->text('html');
            $table->text('description')->nullable();
            $table->string('categories', 1024)->nullable();
            $table->string('tags', 1024)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
        try {
            DB::statement('ALTER TABLE posts ADD FULLTEXT `search` (`description`, `categories`, `tags`)');
        } catch (\Throwable $th) {
            //throw $th;
        }

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
