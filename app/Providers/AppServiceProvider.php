<?php

namespace App\Providers;

use App\Services\CurrencyRateService;
use App\Services\GitService;
use App\Services\LoadService;
use App\Services\MonthService;
use App\Services\RestoreService;
use App\Services\SeedService;
use App\Services\WeekService;
use App\Services\YClientsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    private function registerServices()
    {
        $this->app->singleton(WeekService::class);
        $this->app->singleton(MonthService::class);
        $this->app->singleton(RestoreService::class);
        $this->app->singleton(CurrencyRateService::class);
        $this->app->singleton(YClientsService::class);
        $this->app->singleton(GitService::class);
        $this->app->singleton(LoadService::class);
        $this->app->singleton(SeedService::class);
    }
}
