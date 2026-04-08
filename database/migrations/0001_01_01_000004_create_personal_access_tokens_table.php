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
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->comment('Table of Laravel Framwork, used to manage personal access.');
            $table->id()->comment('token id');
            $table->morphs('tokenable');
            $table->string('name')->comment('token name');
            $table->string('token', 64)->unique()->comment('token');
            $table->text('abilities')->nullable()->comment('authority');
            $table->timestamp('last_used_at')->nullable()->comment('last use date');
            $table->timestamp('expires_at')->nullable()->comment('date of expiry');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
