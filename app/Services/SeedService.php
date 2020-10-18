<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Contact;
use App\Models\Team;

class SeedService
{
    public function contacts(string $startDate, string $endDate, $teams)
    {
        Contact::seed($startDate, $endDate, $teams);
    }

    public function teamOutcomes(string $startDate, string $endDate)
    {
        if (
            $startDate != week()->monday($startDate) ||
            $endDate != week()->sunday($endDate)
        ) return;

        Team::seedOutcomes($startDate, $endDate);
    }

    public function customOutcomes(string $startDate, string $endDate)
    {
        if (
            $startDate != week()->monday($startDate) ||
            $endDate != week()->sunday($endDate)
        ) return;

        Budget::seedCustomOutcomes($startDate, $endDate);
    }
}
