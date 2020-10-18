<?php

namespace App\Services;

use App\Models\Contact;

class SeedService
{
    public function contacts(string $startDate, string $endDate, $teams)
    {
        Contact::seed($startDate, $endDate, $teams);
    }
}
