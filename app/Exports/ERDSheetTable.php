<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\DB;

class ERDSheetTable implements FromCollection, WithHeadings, WithColumnWidths, WithTitle, WithEvents
{
    protected $table = null;
    protected $rows = 0;
    protected $comments = [
        'attachmentable' => 'Table of Orchid Platform, used to manage attachments.',
        'attachments' => 'Table of Orchid Platform, used to manage attachments.',
        'cache' => 'Table of Laravel Framwork, used to manage cache.',
        'cache_locks' => 'Table of Laravel Framwork, used to manage cache.',
        'comments' => 'Table comments, used to manage comments in posts.',
        'failed_jobs' => 'Table of Laravel Framwork, used to manage job and queue.',
        'job_batches' => 'Table of Laravel Framwork, used to manage job and queue.',
        'jobs' => 'Table of Laravel Framwork, used to manage job and queue.',
        'migrations' => 'Table of Laravel Framwork, used to manage migrate.',
        'notifications' => 'Table of Orchid Platform, used to manage notification.',
        'order_items' => 'Table order items, used to manage order item in transaction.',
        'password_reset_tokens' => 'Table of Laravel Framwork, used to manage user reset password.',
        'personal_access_tokens' => 'Table of Laravel Framwork, used to manage personal access.',
        'posts' => 'Table posts, used to manage posts.',
        'products' => 'Table products, use to manage products',
        'role_users' => 'Table of Orchid Platform, used to manage roles.',
        'roles' => 'Table of Orchid Platform, used to manage roles.',
        'sessions' => 'Table of Laravel Framwork, used to manage sessions.',
        'team_invitations' => 'Table of Laravel JetStream, used to manage teams.',
        'team_user' => 'Table of Laravel JetStream, used to manage teams.',
        'teams' => 'Table of Laravel JetStream, used to manage teams.',
        'telescope_entries' => 'Table of telescope, used to manage debug data.',
        'telescope_entries_tags' => 'Table of telescope, used to manage debug data.',
        'telescope_monitoring' => 'Table of telescope, used to manage debug data.',
        'transactions' => 'Table transactions, used to manage transactions.',
        'user_additional_information_use' => 'Table user additional information, used to manage additional information for user.',
        'user_additional_informations' => 'Table user additional information, used to manage additional information for user.',
        'user' => 'Table of Laravel Framwork, used to manage users.',
        'viewers' => 'Table viewers, used to manage viewers in posts.',
    ];

    public function __construct($table) {
        $this->table = $table;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $columns = DB::select("SHOW FULL COLUMNS FROM {$this->table}");

        $result = [];
        foreach ($columns as $column) {
            $name = $column->Field;
            $type = $column->Type;
            $key = $column->Key;
            $nn = $column->Null === 'NO' ? true : false;
            $default = $column->Default;
            $comment = $column->Comment;
            array_push($result, [
                $name, $type, $key, $nn, $default, $comment,
            ]);
            $this->rows = $this->rows + 1;
        }
        return collect($result);
    }

    public function title(): string
    {
        return $this->table;
    }

    public function headings(): array
    {
        return [
            [
                $this->table,
                array_key_exists($this->table, $this->comments) ? $this->comments[$this->table] : '',
            ],
            [
                'Column',
                'Datatype',
                'key',
                'Not Null',
                'Default',
                'Memo',
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 10,
            'D' => 10,
            'E' => 20,
            'F' => 80,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $styleArray = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $sheet->getStyle('A1:F' . ($this->rows + count($this->headings())))->applyFromArray($styleArray);
                $sheet->getStyle('A1:A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFF'], // White text
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '4CAF50'], // Green background
                    ],
                ]);
                $sheet->mergeCells('B1:F1'); 
                $sheet->getStyle('A2:F2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFF'], // White text
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '4CAF50'], // Green background
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                    ],
                ]);
            },
        ];
    }
}
