<?php

namespace App\Services;

use App\Models\Master;
use App\Models\Service;

class LoadService
{
    public function masters(array $items)
    {
        Master::seed($items);
    }

    public function services(array $items)
    {
        Service::seed($items);
    }
}
