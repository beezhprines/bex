<?php

namespace Tests;

use App\Services\RestoreService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function registerApp()
    {
        $this->RestoreService = $this->app->make(RestoreService::class);
    }
}
