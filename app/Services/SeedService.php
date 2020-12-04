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

    public function teamOutcomes(string $date)
    {
        Team::seedOutcomes($date);
    }

    public function customOutcomes(string $startDate, string $endDate)
    {
        Budget::seedCustomOutcomes($startDate, $endDate);
    }
}
