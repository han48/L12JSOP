<?php

namespace App\Console\Commands;

use App\Exports\ERD;
use Maatwebsite\Excel\Facades\Excel;

class GenerateERD extends \BeyondCode\ErdGenerator\GenerateDiagramCommand
{

    /**
     * Execute the console command.
     */
    public function handle()
    {
        parent::handle();
        
        $filename = $this->getOutputFileName();
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        $filename = $filename . '.xlsx';
        Excel::store(new ERD(), $filename);
    }
}
