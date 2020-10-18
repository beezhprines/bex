<?php

namespace App\Jobs;

use App\Services\LoadService;
use App\Services\YClientsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LoadServicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $staff_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $staff_id = null)
    {
        $this->staff_id = $staff_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(YClientsService $yClientsService, LoadService $loadService)
    {
        $yClientsService->authorize();
        $loadService->services($yClientsService->getServices($this->staff_id));
    }
}
