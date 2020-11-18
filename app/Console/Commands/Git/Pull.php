<?php

namespace App\Console\Commands\Git;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Pull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git:pull
    {--branch= : Git current branch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and pull origin';

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
        if (empty($this->option("branch"))) {
            return;
        }

        $branch = $this->option("branch");

        exec("git fetch origin {$branch}");
        $this->info("Branch {$branch} fetched");

        exec("git pull origin {$branch}");
        $this->info("Branch {$branch} merged");

        Artisan::call("optimize:clear");
        exec("composer dump-autoload");
        $this->info("Files refreshed");
    }
}
