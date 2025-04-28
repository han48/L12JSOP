<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\DB;

class ERD implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        $tables = DB::select('SHOW TABLES');
        $result = [new ERDSheet("ERD")];
        foreach ($tables as $value) {
            $table = array_values(get_object_vars($value))[0];
            array_push($result, new ERDSheetTable($table));
        }
        return $result;
    }
}
