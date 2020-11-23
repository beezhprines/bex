<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Restore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore
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
    public function handle()
    {

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
        $contents = file_get_contents($url);
        Storage::put("dshpyrk3_bex_prd_backup.sql", $contents);
        $this->info("Master database downloaded to /storage/app/dshpyrk3_bex_prd_backup.sql");
        exec($restoreCommand);
        $this->info("{$this->dbname} database restored from dshpyrk3_bex_prd_backup.sql");
    }
}
