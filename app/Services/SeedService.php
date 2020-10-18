<?php

namespace App\Services;

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
        Team::seedOutcomes($startDate, $endDate);
    }
}
