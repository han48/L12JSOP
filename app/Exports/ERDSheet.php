<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ERDSheet implements WithTitle, WithDrawings
{
    protected $table = null;

    public function __construct($table) {
        $this->table = $table;
    }

    public function title(): string
    {
        return $this->table;
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('ERD');
        $drawing->setDescription('ERD');
        $drawing->setPath(storage_path('app/private/erd.jpeg'));
        $drawing->setHeight(2000);
        $drawing->setCoordinates('B2');

        return [$drawing];
    }
}
