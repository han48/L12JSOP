<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ManagementCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'management:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create data management: model, migration, screen, table, menu, permission';

    /**
     * Execute the console command.
     */
    public function handle()
    {

    }
}
