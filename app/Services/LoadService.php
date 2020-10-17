<?php

namespace App\Services;

use App\Models\Master;

class LoadService
{
    public function masters(array $items)
    {
        Master::seed($items);
    }
}
