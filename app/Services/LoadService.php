<?php

namespace App\Services;

use App\Models\CurrencyRate;
use App\Models\Master;
use App\Models\Record;
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

    public function records(array $items, string $date)
    {
        Record::seed($items, $date);
    }

    public function currencyRates(array $items)
    {
        CurrencyRate::seed($items);
    }
}
