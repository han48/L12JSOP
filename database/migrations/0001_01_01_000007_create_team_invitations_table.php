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
        Schema::create('team_invitations', function (Blueprint $table) {
            $table->id()->comment('Team Invitation ID');
            $table->foreignId('team_id')->constrained()->cascadeOnDelete()->comment('Team ID');
            $table->string('email')->comment('Email Address');
            $table->string('role')->nullable()->comment('Role');
            $table->timestamps();

            $table->unique(['team_id', 'email'])->comment('Unique constraint for Team ID and Email Address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_invitations');
    }
};
