<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GitService
{
    private $http;
    private $productionURL;
    private $stagingURL;
    private $hash;

    function __construct()
    {
        $this->productionURL = env('APP_PRODUCTION_URL');
        $this->stagingURL = env('APP_STAGING_URL');
        $this->hash = env('GIT_HASH');
        $this->http = $this->getClient();
    }

    private function getClient()
    {
        return Http::timeout(5)->retry(3, 5000);
    }

    public function pull(string $branch)
    {
        $url = $this->stagingURL;
        switch ($branch) {
            case "staging":
                $url = $this->stagingURL;
                break;

            case "master":
                $url = $this->productionURL;
                break;

            default:
                return false;
                break;
        }

        $this->http->get("{$url}/git/pull", [
            "hash" => $this->hash,
            "branch" => $branch
        ])
            ->throw()
            ->json();
    }
}
