<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GitService
{
    private $filename;
    private $productionURL;
    private $stagingURL;
    private $githash;
    private $http;

    function __construct()
    {
        $this->filename = "version";
        $this->productionURL = env("APP_PRODUCTION_URL");
        $this->stagingURL = env("APP_STAGING_URL");
        $this->githash = env("GIT_HASH");
        $this->http = $this->getClient();
    }

    private function getClient()
    {
        return Http::timeout(5)->retry(3, 5000);
    }

    public function increaseVersion(string $type)
    {
        if (!Storage::exists($this->filename)) return null;

        $version = Storage::get($this->filename);
        $version = explode(".", $version);

        switch ($type) {
            case "patch":
                $version[2] = intval($version[2]) + 1;
                break;

            case "minor":
                $version[1] = intval($version[1]) + 1;
                $version[2] = 0;
                break;

            case "major":
                $version[0] = intval($version[0]) + 1;
                $version[1] = 0;
                $version[2] = 0;
                break;

            default:
                $version[2] = intval($version[2]) + 1;
                break;
        }
        $version = implode(".", $version);
        Storage::put($this->filename, $version);
        return $version;
    }

    public function version()
    {
        return Storage::exists($this->filename) ? Storage::get($this->filename) : null;
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

        $this->http->get("{$url}/pull", [
            "githash" => $this->githash,
            "branch" => $branch
        ])
            ->throw()
            ->json();
    }
}
