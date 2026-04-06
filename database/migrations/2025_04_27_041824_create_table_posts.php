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
            $table->id()->comment('Post ID');
            $table->foreignId('author_id')->comment('Author ID');
            $table->string('slug')->unique()->nullable()->comment('Slug');
            $table->string('title')->comment('Title');
            $table->string('image', 2048)->nullable()->comment('Image');
            $table->text('html')->comment('HTML Content');
            $table->text('description')->nullable()->comment('Description');
            $table->string('categories', 1024)->nullable()->comment('Categories');
            $table->string('tags', 1024)->nullable()->comment('Tags');
            $table->tinyInteger('status')->default(0)->comment('Status');
            $table->softDeletes()->comment('Soft Delete');
            $table->timestamps();
        });
        try {
            DB::statement('ALTER TABLE posts ADD FULLTEXT `search` (`description`, `categories`, `tags`)');
        } catch (\Throwable $th) {
            //throw $th;
        }

        Schema::create('comments', function (Blueprint $table) {
            $table->id()->comment('Comment ID');
            $table->foreignId('author_id')->comment('Author ID');
            $table->foreignId('post_id')->comment('Post ID');
            $table->text('content')->comment('Content');
            $table->tinyInteger('status')->default(0)->comment('Status');
            $table->softDeletes()->comment('Soft Delete');
            $table->timestamps();
        });

        Schema::create('viewers', function (Blueprint $table) {
            $table->id()->comment('Viewer ID');
            $table->foreignId('author_id')->comment('Author ID');
            $table->foreignId('post_id')->comment('Post ID');
            $table->tinyInteger('status')->default(0)->comment('Status');
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
