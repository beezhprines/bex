<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Contact;
use App\Models\Team;
use ResponseCache;

class SeedService
{
    public function contacts(string $startDate, string $endDate, $teams)
    {
        Contact::seed($startDate, $endDate, $teams);
        ResponseCache::clear();
    }

    public function teamOutcomes(string $date)
    {
        Team::seedOutcomes($date);
        ResponseCache::clear();
    }

    public function customOutcomes(string $startDate, string $endDate)
    {
        Budget::seedCustomOutcomes($startDate, $endDate);
        ResponseCache::clear();
    }
}
