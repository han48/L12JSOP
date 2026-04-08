<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the migration connection name.
     */
    public function getConnection(): ?string
    {
        return config('telescope.storage.database.connection');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $schema = Schema::connection($this->getConnection());

        $schema->create('telescope_entries', function (Blueprint $table) {
            $table->comment('Telescope Entries Table');
            $table->bigIncrements('sequence')->comment('Sequence');
            $table->uuid('uuid')->comment('UUID');
            $table->uuid('batch_id')->comment('Batch ID');
            $table->string('family_hash')->nullable()->comment('Family Hash');
            $table->boolean('should_display_on_index')->default(true)->comment('Should Display on Index');
            $table->string('type', 20)->comment('Type');
            $table->longText('content')->comment('Content');
            $table->dateTime('created_at')->nullable()->comment('Created Date and Time');

            $table->unique('uuid')->comment('Unique constraint for UUID');
            $table->index('batch_id')->comment('Index for Batch ID');
            $table->index('family_hash')->comment('Index for Family Hash');
            $table->index('created_at')->comment('Index for Created Date and Time');
            $table->index(['type', 'should_display_on_index'])->comment('Composite index for Type and Display Index Flag');
        });

        $schema->create('telescope_entries_tags', function (Blueprint $table) {
            $table->comment('Telescope Entries Tags Table');
            $table->uuid('entry_uuid')->comment('Entry UUID');
            $table->string('tag')->comment('Tag');

            $table->primary(['entry_uuid', 'tag'])->comment('Composite primary key for Entry UUID and Tag');
            $table->index('tag')->comment('Index for Tag');

            $table->foreign('entry_uuid')
                ->references('uuid')
                ->on('telescope_entries')
                ->onDelete('cascade')
                ->comment('Foreign key constraint for Entry UUID');
        });

        $schema->create('telescope_monitoring', function (Blueprint $table) {
            $table->comment('Telescope Monitoring Table');
            $table->string('tag')->primary()->comment('Tag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $schema = Schema::connection($this->getConnection());

        $schema->dropIfExists('telescope_entries_tags');
        $schema->dropIfExists('telescope_entries');
        $schema->dropIfExists('telescope_monitoring');
    }
};
