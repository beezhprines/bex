<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application"s command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $this->daily($schedule);
        $this->weekly($schedule);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . "/Commands");

        require base_path("routes/console.php");
    }

    public function daily(Schedule $schedule)
    {
        $date = date(config("app.iso_date"), strtotime("-1 days"));

        $this->grab($schedule, $date);
        $this->solve($schedule, $date);
    }

    private function weekly(Schedule $schedule)
    {
        $this->seed($schedule);

        $startDate = date(config("app.iso_date"), strtotime("monday previous week"));
        $endDate = date(config("app.iso_date"), strtotime("sunday previous week"));

        $schedule->command("load --all --startDate={$startDate} --endDate={$endDate}")->weeklyOn(1, "01:20");
        $schedule->command("solve --all --startDate={$startDate} --endDate={$endDate}")->weeklyOn(1, "01:40");
    }

    private function seed(Schedule $schedule)
    {
        $startDate = date(config("app.iso_date"), strtotime("monday this week"));
        $endDate = date(config("app.iso_date"), strtotime("sunday this week"));

        $schedule->command("seed --all --startDate={$startDate} --endDate={$endDate}")->weeklyOn(1, "01:00");
    }

    private function grab(Schedule $schedule, string $date)
    {
        $schedule->command("load --all --startDate={$date} --endDate={$date}")->dailyAt("02:00");
    }

    private function solve(Schedule $schedule, string $date)
    {
        $schedule->command("solve --all --date={$date}")->dailyAt("02:30");
    }
}
