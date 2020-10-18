<?php

namespace App\Console\Commands;

use App\Jobs\SolveMastersComissionJob;
use App\Jobs\SolveMastersProfitJob;
use App\Jobs\SolveOutcomesJob;
use App\Jobs\SolveTotalComissionJob;
use Illuminate\Console\Command;

class Solve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'solve
    {--total-comission : Solve total comission}
    {--masters-comission : Solve masters comission}
    {--masters-profit : Solve masters profit}
    {--custom-outcomes : Solve custom outcomes}
    {--all : Solve all budgets}
    {--date= : For date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Solve budgets';

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
        if (empty($this->option('date'))) return;

        $date = $this->option('date');

        if ($this->option('total-comission')) {
            SolveTotalComissionJob::dispatchNow($date);
            return;
        }

        if ($this->option('masters-comission')) {
            SolveMastersComissionJob::dispatchNow($date);
            return;
        }

        if ($this->option('masters-profit')) {
            SolveMastersProfitJob::dispatchNow($date);
            return;
        }

        if ($this->option('custom-outcomes')) {
            SolveOutcomesJob::dispatchNow($date);
            return;
        }
    }
}
