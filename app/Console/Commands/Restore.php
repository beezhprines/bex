<?php

namespace App\Console\Commands;

use App\Services\LoadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Restore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore
    {--backup : Make database backup}
    {--env= : Environment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore database from production';

    private $masterUrl;
    private $githash;
    private $dbusername;
    private $dbpasssword;
    private $dbname;

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
        $this->dbusername = env("DB_USERNAME");
        $this->dbpasssword = env("DB_PASSWORD");
        $this->dbname = env("DB_DATABASE");
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LoadService $loadService)
    {
        if (!empty($this->option("backup"))) {
            exec("sh ~/.local/bin/bex/export_master_db.sh");
            return;
        }

        if (empty($this->option("env"))) {
            $this->error("Environment not provided");
            return;
        }

        switch ($this->option("env")) {
            case 'local':
                $restoreCommand = 'cmd.exe /c "mysql -u ' . $this->dbusername . ' -p' . $this->dbpasssword . ' ' . $this->dbname . ' < storage/app/dshpyrk3_bex_prd_backup.sql"';
                break;
            case 'staging':
                $restoreCommand = 'mysql -u ' . $this->dbusername . ' -p' . $this->dbpasssword . ' ' . $this->dbname . ' < storage/app/dshpyrk3_bex_prd_backup.sql';
                break;

            default:
                $restoreCommand = "";
                break;
        }

        $url = "{$this->masterUrl}/db/backup?githash={$this->githash}";

        $this->info("Starting download from {$this->masterUrl}/db/backup");

        $path = storage_path("\app\dshpyrk3_bex_prd_backup.sql");

        $response = $loadService->download($url, $path, $this->output);

        if ($response["code"] ?? 0 == 200) {
            $this->info("\nSuccess downloaded to $path");
            Artisan::call("migrate:fresh");
            exec($restoreCommand);

            $this->info("{$this->dbname} database restored from $path");
        } else {
            $this->error("Error occured");
        }
    }
}
