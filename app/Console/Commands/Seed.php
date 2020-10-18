<?php

namespace App\Console\Commands;

use App\Jobs\SeedContactsJob;
use App\Jobs\SeedOutcomesJob;
use App\Services\RestoreService;
use Illuminate\Console\Command;

class Seed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed
    {--restore : Restore from backup}
    {--contacts : Seed contacts}
    {--team-outcomes : Seed outcome budgets for all team}
    {--custom-outcomes : Seed custom outcomes}
    {--startDate= : From date}
    {--endDate= : To date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed entities';

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
    public function handle(RestoreService $restoreService)
    {
        if ($this->option('restore')) {
            $restoreService->restore();
            return;
        }

        if (empty($this->option('startDate')) || empty($this->option('endDate'))) {
            $this->error("Start date or end date invalid");
            return;
        }

        $from = $this->option('startDate');
        $to = $this->option('endDate');

        if ($this->option('contacts')) {
            SeedContactsJob::dispatchNow($from, $to);
        }

        if ($this->option('team-outcomes')) {
            SeedOutcomesJob::dispatchNow($from, $to, "team");
        }

        if ($this->option('custom-outcomes')) {
            SeedOutcomesJob::dispatchNow($from, $to, "custom");
        }
    }
}
