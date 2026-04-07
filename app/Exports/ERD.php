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
        usort($tables, function ($a, $b) {
            $special = [
                'jobs', 'job_batches','failed_jobs',
                'cache', 'cache_locks',
                'role_users', 'roles',
                'attachmentable', 'attachments',
                'password_reset_tokens',
                'personal_access_tokens', 'sessions',
                'teams', 'team_user', 'team_invitations',
                'telescope_entries', 'telescope_entries_tags', 'telescope_monitoring',
                'migrations',
            ];

            $default = [
                'users',
            ];

            $aIsSpecial = in_array($a->Tables_in_fitness, $special);
            $bIsSpecial = in_array($b->Tables_in_fitness, $special);
            $aIsNormal = in_array($a->Tables_in_fitness, $default);
            $bIsNormal = in_array($b->Tables_in_fitness, $default);

            if ($aIsNormal && !$bIsNormal) return -1;
            if ($aIsSpecial && !$bIsSpecial) return 1;
            if (!$aIsSpecial && $bIsSpecial) return -1;

            return strcmp($a->Tables_in_fitness, $b->Tables_in_fitness);
        });

        $result = [new ERDSheet("ERD")];
        foreach ($tables as $value) {
            $table = array_values(get_object_vars($value))[0];
            array_push($result, new ERDSheetTable($table));
        }
        return $result;
    }
}
