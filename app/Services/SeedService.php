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
            $this->startDate != week()->monday($this->startDate) ||
            $this->endDate != week()->sunday($this->endDate)
        ) return;

        Team::seedOutcomes($startDate, $endDate);
    }

    public function customOutcomes(string $startDate, string $endDate)
    {
        Budget::seedCustomOutcomes($startDate, $endDate);
    }
}
