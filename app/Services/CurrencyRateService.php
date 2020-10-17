<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyRateService
{
    private $http;
    private $baseURL;
    private $accessKey;

    function __construct()
    {
        $this->baseURL = env('CURRENCY_RATE_SERVICE_BASEURL');
        $this->accessKey = env('CURRENCY_RATE_SERVICE_ACCESSKEY');
        $this->http = $this->getClient();
    }

    private function getClient()
    {
        return Http::timeout(5)->retry(3, 5000);
    }

    public function getRates()
    {
        $rates = $this->http->get($this->baseURL, [
            'access_key' => $this->accessKey
        ])
            ->throw()
            ->json();

        if (empty($rates) || empty($rates['success']) || !$rates['success']) {
            return null;
        }

        return $rates;
    }
}
