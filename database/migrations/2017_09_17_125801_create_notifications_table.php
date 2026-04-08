<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->comment('Table of Laravel Framwork, used to manage notifications.');
                $table->uuid('id')->primary()->comment('Notification ID');
                $table->string('type')->comment('Notification type');
                $table->morphs('notifiable');
                $table->text('data')->comment('Notification data');
                $table->timestamp('read_at')->nullable()->comment('Read timestamp');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
