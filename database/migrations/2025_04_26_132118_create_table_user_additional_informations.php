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
        Schema::create('user_additional_informations', function (Blueprint $table) {
            $table->comment('Table of Laravel Framwork, used to manage user additional informations.');
            $table->id()->comment('User Additional Information ID');
            $table->string('slug')->comment('Slug');
            $table->string('name')->comment('Name');
            $table->string('memo')->comment('Memo');
            $table->timestamps();
        });

        Schema::create('user_additional_information_user', function (Blueprint $table) {
            $table->comment('Table of Laravel Framwork, used to manage user additional information relations.');
            $table->id()->comment('User Additional Information Relation ID');
            $table->foreignId('user_id')->comment('User ID');
            $table->foreignId('user_additional_information_id')->comment('User Additional Information ID');
            $table->string('value')->comment('Value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_additional_informations');
        Schema::dropIfExists('user_additional_information_user');
    }
};
