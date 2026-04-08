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
        Schema::create('team_user', function (Blueprint $table) {
            $table->comment('Table of Laravel Framwork, used to manage team user.');
            $table->id()->comment('Team user ID');
            $table->foreignId('team_id')->comment('Team ID');
            $table->foreignId('user_id')->comment('User ID');
            $table->string('role')->nullable()->comment('roll');
            $table->timestamps();

            $table->unique(['team_id', 'user_id'])->comment('Unique constraint for combination of team ID and user ID');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};
