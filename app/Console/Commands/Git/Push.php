<?php

namespace App\Console\Commands\Git;

use App\Services\GitService;
use Illuminate\Console\Command;

class Push extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'git:push
    {--branch= : Current branch}
    {--patch : Increase patch version of app}
    {--minor : Increase minor version of app}
    {--major : Increase minor version of app}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push changes to git';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GitService $gitService)
    {
        parent::__construct();

        $this->gitService = $gitService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $branch = $this->option('branch') ?: "staging";

        switch ($this->option('branch')) {
            case 'staging':

                exec("git checkout staging");
                $this->info('Checkout branch staging');

                $type = "patch";
                if ($this->option('minor')) $type = "minor";
                if ($this->option('major')) $type = "major";

                $version = $this->gitService->increaseVersion($type);
                $this->info("Version {$version} of app setted");

                exec('git add .');
                $this->info('Added all files to stage');

                $message = $this->ask('Set commit message');

                exec('git commit -m "' . $message . '" ');
                $this->info('Changes commited');

                exec("git tag v{$version}");
                $this->info('Tag added');

                exec('git push origin staging --tags');
                $this->info('staging branch pushed');

                break;

            case 'master':
                exec("git checkout master");
                $this->info("Checkout branch master");

                exec("git merge staging");
                $this->info("Merge master with staging");

                exec('git push origin master');
                $this->info('master branch pushed');
                break;

            default:
                $this->error("No branch selected");
                return;

                break;
        }

        $this->gitService->pull($branch);
        $this->info("{$branch} git pulled");

        exec("git checkout staging");
        $this->info('Checkout branch staging');
    }
}
