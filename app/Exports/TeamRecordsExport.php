<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeamRecordsExport implements FromArray, WithHeadings
{
    protected $teams;

    public function __construct(array $teams)
    {
        $this->teams = $teams;
    }

    public function array(): array
    {
        return $this->teams;
    }

    public function headings(): array
    {
        return ["Команда", "Записались конв.", "Записались по акции"];
    }
}
