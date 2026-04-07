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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id()->comment('job ID');
            $table->string('queue')->index()->comment('queue');
            $table->longText('payload')->comment('payload');
            $table->unsignedTinyInteger('attempts')->comment('number of attempts');
            $table->unsignedInteger('reserved_at')->nullable()->comment('Reservation date and time');
            $table->unsignedInteger('available_at')->comment('Availability dates');
            $table->unsignedInteger('created_at')->comment('Creation date and time');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary()->comment('Job batch ID');
            $table->string('name')->comment('batch name');
            $table->integer('total_jobs')->comment('Total number of jobs');
            $table->integer('pending_jobs')->comment('Number of pending jobs');
            $table->integer('failed_jobs')->comment('Number of failed jobs');
            $table->longText('failed_job_ids')->comment('Failed job ID');
            $table->mediumText('options')->nullable()->comment('option');
            $table->integer('cancelled_at')->nullable()->comment('Cancellation date and time');
            $table->integer('created_at')->comment('Creation date and time');
            $table->integer('finished_at')->nullable()->comment('End date and time');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id()->comment('Failed job ID');
            $table->string('uuid')->unique()->comment('UUID');
            $table->text('connection')->comment('connection');
            $table->text('queue')->comment('queue');
            $table->longText('payload')->comment('payload');
            $table->longText('exception')->comment('exception');
            $table->timestamp('failed_at')->useCurrent()->comment('Failure date and time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
