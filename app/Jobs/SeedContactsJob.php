<?php

namespace App\Jobs;

use App\Models\Team;
use App\Services\SeedService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SeedContactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $startDate;
    private $endDate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $startDate, string $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SeedService $seedService)
    {
        $seedService->contacts($this->startDate, $this->endDate, Team::all());
    }
}
