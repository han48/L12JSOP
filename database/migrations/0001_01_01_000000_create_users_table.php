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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('User ID');
            $table->string('name')->comment('username');
            $table->string('email')->unique()->comment('email address');
            $table->timestamp('email_verified_at')->nullable()->comment('Email address confirmation date and time');
            $table->string('password')->comment('password');
            $table->rememberToken()->comment('Token to stay logged in');
            $table->foreignId('current_team_id')->nullable()->comment('Current team ID');
            $table->string('profile_photo_path', 2048)->nullable()->comment('Profile photo path');
            $table->tinyInteger('status')->default(1)->comment('user state');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary()->comment('email address');
            $table->string('token')->comment('password reset token');
            $table->timestamp('created_at')->nullable()->comment('Creation date and time');
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary()->comment('Session ID');
            $table->foreignId('user_id')->nullable()->index()->comment('User ID');
            $table->string('ip_address', 45)->nullable()->comment('IP address');
            $table->text('user_agent')->nullable()->comment('user agent');
            $table->longText('payload')->comment('session data');
            $table->integer('last_activity')->index()->comment('Last event date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
