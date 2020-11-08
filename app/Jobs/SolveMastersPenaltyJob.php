<?php

namespace App\Jobs;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SolveMastersPenaltyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $date)
    {
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $startDate = week()->monday(week()->previous($this->date));
        $endDate = week()->sunday(week()->previous($this->date));
        Budget::solveMastersPenalty($this->date, $startDate, $endDate);

        echo "Masters penalties solved for date {$this->date}\n";
    }
}
