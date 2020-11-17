<?php

namespace App\Console\Commands;

use App\Jobs\SolveManagersProfitJob;
use App\Jobs\SolveMastersComissionJob;
use App\Jobs\SolveMastersPenaltyJob;
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
    protected $signature = "solve
    {--total-comission : Solve total comission}
    {--masters-comission : Solve masters comission}
    {--masters-profit : Solve masters profit}
    {--masters-penalty : Solve masters penlaty}
    {--custom-outcomes : Solve custom outcomes}
    {--managers-profit : Solve managers profit}
    {--operators-profit : Solve operators profit}
    {--all : Solve all budgets}
    {--date= : For date}
    {--startDate= : From date}
    {--endDate= : To date}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Solve budgets";

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
        $dates = [];
        if (!empty($this->option("date"))) {
            $dates[] = $this->option("date");
        } elseif (!empty($this->option("startDate")) && !empty($this->option("endDate"))) {

            $from = $this->option("startDate");
            $to = $this->option("endDate");

            foreach (daterange($from, $to, true) as $date) {
                $date = date_format($date, config("app.iso_date"));
                $dates[] = $date;
            }
        } else {
            echo "Dates not proided";
            return;
        };

        if ($this->option("all")) {
            foreach ($dates as $date) {
                SolveTotalComissionJob::dispatchNow($date);
                SolveMastersComissionJob::dispatchNow($date);
                SolveMastersProfitJob::dispatchNow($date);
                SolveMastersPenaltyJob::dispatchNow($date);
                SolveOutcomesJob::dispatchNow($date);
                SolveManagersProfitJob::dispatchNow($date);
                SolveOperatorsProfitJob::dispatchNow($date);
            }
            return;
        }

        if ($this->option("total-comission")) {
            foreach ($dates as $date) {
                SolveTotalComissionJob::dispatchNow($date);
            }
            return;
        }

        if ($this->option("masters-comission")) {
            foreach ($dates as $date) {
                SolveMastersComissionJob::dispatchNow($date);
            }
            return;
        }

        if ($this->option("masters-penalty")) {
            foreach ($dates as $date) {
                SolveMastersPenaltyJob::dispatchNow($date);
            }
            return;
        }

        if ($this->option("masters-profit")) {
            foreach ($dates as $date) {
                SolveMastersProfitJob::dispatchNow($date);
            }
            return;
        }

        if ($this->option("custom-outcomes")) {
            foreach ($dates as $date) {
                SolveOutcomesJob::dispatchNow($date);
            }
            return;
        }

        if ($this->option("managers-profit")) {
            foreach ($dates as $date) {
                SolveManagersProfitJob::dispatchNow($date);
            }
            return;
        }

        if ($this->option("operators-profit")) {
            foreach ($dates as $date) {
                SolveOperatorsProfitJob::dispatchNow($date);
            }
            return;
        }
    }
}
