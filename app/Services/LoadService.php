<?php

namespace App\Services;

use App\Models\Cosmetologist;
use App\Models\CurrencyRate;
use App\Models\Master;
use App\Models\Record;
use App\Models\Service;
use GuzzleHttp\Client;

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

    public function download($url, $path, $io)
    {
        $client = new Client(array(
            "progress" => function ($total, $downloaded) use ($io, &$progress) {
                if ($total > 0 && is_null($progress)) {
                    $progress = $io->createProgressBar($total);
                    $progress->start();
                }

                if (!is_null($progress)) {
                    if ($total === $downloaded) {
                        $progress->finish();

                        return;
                    }
                    $progress->setProgress($downloaded);
                }
            },
            "sink" => $path,
        ));
        $response = $client->get($url);
        return ["code" => $response->getStatusCode(), "path" => $path];
    }
}
