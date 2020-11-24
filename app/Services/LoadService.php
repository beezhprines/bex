<?php

namespace App\Services;

use App\Models\Cosmetologist;
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

    public function cosmetologists(array $items)
    {
        Cosmetologist::seed($items);
    }

    public function services(array $items)
    {
        Service::seed($items);
    }

    public function records(array $items)
    {
        Record::seed($items);
    }

    public function currencyRates(array $items)
    {
        CurrencyRate::seed($items);
    }
}
