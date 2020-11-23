<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class Restore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database from production';

    private $masterUrl;
    private $githash;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->masterUrl = env('APP_PRODUCTION_URL');
        $this->githash = env("GIT_HASH");
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $backup = Http::get("{$this->masterUrl}/db/backup", [
            "githash" => $this->githash
        ])->throw();

        dd($backup);
    }
}
