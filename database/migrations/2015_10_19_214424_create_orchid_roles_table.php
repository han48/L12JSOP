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
        Schema::create('roles', function (Blueprint $table): void {
            $table->comment('Table of Laravel Framwork, used to manage roles.');
            $table->increments('id')->comment('Role ID');
            $table->string('slug')->unique()->comment('Slug');
            $table->string('name')->comment('Role Name');
            $table->jsonb('permissions')->nullable()->comment('Permissions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
