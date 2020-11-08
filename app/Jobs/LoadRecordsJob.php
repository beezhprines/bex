<?php

namespace App\Jobs;

use App\Services\LoadService;
use App\Services\YClientsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadRecordsJob implements ShouldQueue
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
    public function handle(YClientsService $yClientsService, LoadService $loadService)
    {
        $yClientsService->authorize();
        $loadService->records($yClientsService->getRecords($this->date), $this->date);
        echo "Records loaded for date {$this->date}\n";
    }
}
