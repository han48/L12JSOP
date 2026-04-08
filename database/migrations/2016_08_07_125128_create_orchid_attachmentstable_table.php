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
        Schema::create('attachments', function (Blueprint $table) {
            $table->comment('Table of Laravel Framwork, used to manage attachments.');
            $table->increments('id')->comment('Attachment ID');
            $table->text('name')->comment('File Name');
            $table->text('original_name')->comment('Original File Name');
            $table->string('mime')->comment('MIME Type');
            $table->string('extension')->nullable()->comment('File Extension');
            $table->bigInteger('size')->default(0)->comment('File Size');
            $table->integer('sort')->default(0)->comment('Sort Order');
            $table->text('path')->comment('File Path');
            $table->text('description')->nullable()->comment('Description');
            $table->text('alt')->nullable()->comment('Alt Text');
            $table->text('hash')->nullable()->comment('Hash');
            $table->string('disk')->default('public')->comment('Disk');
            $table->unsignedBigInteger('user_id')->nullable()->comment('User ID');
            $table->string('group')->nullable()->comment('Group');
            $table->timestamps();
        });

        Schema::create('attachmentable', function (Blueprint $table) {
            $table->comment('Table of Laravel Framwork, used to manage attachment relations.');
            $table->increments('id')->comment('Attachment Relation ID');
            $table->string('attachmentable_type')->comment('Related Type');
            $table->unsignedInteger('attachmentable_id')->comment('Related ID');
            $table->unsignedInteger('attachment_id')->comment('Attachment ID');

            $table->index(['attachmentable_type', 'attachmentable_id'])->comment('Related Index');

            $table->foreign('attachment_id')
                ->references('id')
                ->on('attachments')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->comment('Foreign key constraint for Attachment ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('attachmentable');
        Schema::drop('attachments');
    }
};
