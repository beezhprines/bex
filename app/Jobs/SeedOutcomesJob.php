<?php

namespace App\Jobs;

use App\Services\SeedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SeedOutcomesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $startDate;
    private $endDate;
    private $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $startDate, string $endDate, string $type)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SeedService $seedService)
    {
        switch ($this->type) {
            case 'team':
                $seedService->teamOutcomes($this->startDate, $this->endDate);
                break;

            default:
                # code...
                break;
        }
    }
}
