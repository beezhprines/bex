<?php

namespace App\Services;

use App\Models\Cosmetologist;
use App\Models\CurrencyRate;
use App\Models\Master;
use App\Models\Record;
use App\Models\Service;
use GuzzleHttp\Client;
use ResponseCache;

class LoadService
{
    public function masters(array $items)
    {
        Master::seed($items);
        ResponseCache::clear();
    }

    public function cosmetologists(array $items)
    {
        Cosmetologist::seed($items);
        ResponseCache::clear();
    }

    public function services(array $items)
    {
        Service::seed($items);
        ResponseCache::clear();
    }

    public function records(array $items, string $date)
    {
        Record::seed($items, $date);
        ResponseCache::clear();
    }

    public function currencyRates(array $items)
    {
        CurrencyRate::seed($items);
        ResponseCache::clear();
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
