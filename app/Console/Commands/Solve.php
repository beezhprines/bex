<?php

namespace App\Console\Commands;

use App\Jobs\SolveManagersProfitJob;
use App\Jobs\SolveMastersComissionJob;
use App\Jobs\SolveMastersProfitJob;
use App\Jobs\SolveOperatorsProfitJob;
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
    {--managers-profit : Solve managers profit}
    {--operators-profit : Solve operators profit}
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

        if ($this->option('all')) {
            SolveTotalComissionJob::dispatchNow($date);
            SolveMastersComissionJob::dispatchNow($date);
            SolveMastersProfitJob::dispatchNow($date);
            SolveOutcomesJob::dispatchNow($date);
            SolveManagersProfitJob::dispatchNow($date);
            SolveOperatorsProfitJob::dispatchNow($date);
            return;
        }

        if ($this->option('total-comission')) {
            SolveTotalComissionJob::dispatchNow($date);
            return;
        }

        if ($this->option('masters-comission')) {
            SolveMastersComissionJob::dispatchNow($date);
            return;
        }

        if ($this->option('masters-penalty')) {
            SolveMastersPenaltyJob::dispatchNow($date);
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

        if ($this->option('managers-profit')) {
            SolveManagersProfitJob::dispatchNow($date);
            return;
        }

        if ($this->option('operators-profit')) {
            SolveOperatorsProfitJob::dispatchNow($date);
            return;
        }
    }
}
