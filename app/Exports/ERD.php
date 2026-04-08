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
        $defaults = [
            'users',
        ];

        $special = [
            'jobs',
            'job_batches',
            'failed_jobs',
            'cache',
            'cache_locks',
            'role_users',
            'roles',
            'attachmentable',
            'attachments',
            'password_reset_tokens',
            'personal_access_tokens',
            'sessions',
            'teams',
            'team_user',
            'team_invitations',
            'telescope_entries',
            'telescope_entries_tags',
            'telescope_monitoring',
            'migrations',
        ];
        $tables = DB::select('SELECT table_name, table_comment FROM information_schema.tables WHERE table_schema = DATABASE()');
        usort($tables, function ($a, $b) use($special, $defaults) {

            $aIsSpecial = in_array($a->{'TABLE_NAME'}, $special);
            $bIsSpecial = in_array($b->{'TABLE_NAME'}, $special);
            $aIsNormal = in_array($a->{'TABLE_NAME'}, $defaults);
            $bIsNormal = in_array($b->{'TABLE_NAME'}, $defaults);

            if ($aIsNormal && !$bIsNormal) return -1;
            if ($aIsSpecial && !$bIsSpecial) return 1;
            if (!$aIsSpecial && $bIsSpecial) return -1;

            return strcmp($a->{'TABLE_NAME'}, $b->{'TABLE_NAME'});
        });

        $result = [new ERDSheet("ERD")];
        foreach ($tables as $table) {
            array_push($result, new ERDSheetTable($table->{'TABLE_NAME'}, $table->{'TABLE_COMMENT'}));
        }
        return $result;
    }
}
