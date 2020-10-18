<?php

namespace App\Console\Commands;

use App\Jobs\LoadCurrencyRatesJob;
use App\Jobs\LoadMastersJob;
use App\Jobs\LoadRecordsJob;
use App\Jobs\LoadServicesJob;
use Illuminate\Console\Command;

class Load extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'load
    {--masters : Load masters}
    {--services : Load services}
    {--records : Load records}
    {--rates : Load currency rates}
    {--startDate= : From date}
    {--endDate= : To date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load entities from yclients';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('masters')) {
            LoadMastersJob::dispatchNow();
        }

        if ($this->option('services')) {
            LoadServicesJob::dispatchNow();
        }

        if ($this->option('rates')) {
            LoadCurrencyRatesJob::dispatchNow();
        }

        if ($this->option('records')) {
            if ($this->option('startDate') && $this->option('endDate')) {
                $from = $this->option('startDate');
                $to = $this->option('endDate');

                foreach (daterange($from, $to, true) as $date) {
                    $date = date_format($date, config('app.iso_date'));
                    LoadRecordsJob::dispatchNow($date);
                }
            }
        }
    }
}
