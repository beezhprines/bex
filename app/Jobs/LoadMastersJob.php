<?php

namespace App\Jobs;

use App\Services\LoadService;
use App\Services\YClientsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadMastersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $origin_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $origin_id = null)
    {
        $this->origin_id = $origin_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(YClientsService $yClientsService, LoadService $loadService)
    {
        $yClientsService->authorize();

        if (!empty($this->origin_id)) {
            $loadService->masters([$yClientsService->findStaff($this->origin_id)]);
        } else {
            $loadService->masters($yClientsService->getStaff());
        }
    }
}
