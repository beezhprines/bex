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
        $filePath = storage_path("schedule_" . isodate() . ".txt");

        $yesterday = date(config("app.iso_date"), strtotime("-1 days"));
        $lastWeekStart = date(config("app.iso_date"), strtotime("monday previous week"));
        $lastWeekEnd = date(config("app.iso_date"), strtotime("sunday previous week"));

        $today = isodate();
        $thisWeekStart = date(config("app.iso_date"), strtotime("monday this week"));
        $thisWeekEnd = date(config("app.iso_date"), strtotime("sunday this week"));

        $schedule->command("load --rates")->dailyAt("03:40")->appendOutputTo($filePath);

        $this->solveDate($yesterday, $filePath, $schedule);

        if (date("D", strtotime($today)) == "Mon") {
            $this->seedNewWeek($thisWeekStart, $thisWeekEnd, $filePath, $schedule);
        }
        if (date("D", strtotime($today)) == "Tue") {
            $this->solveLastWeek($lastWeekStart, $lastWeekEnd, $filePath, $schedule);
        }
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

    private function solveDate(string $date, string $filePath, Schedule $schedule)
    {
        /* LOAD */
        $schedule->command("load --masters")->dailyAt("03:10")->appendOutputTo($filePath);
        $schedule->command("load --cosmetologists")->dailyAt("03:20")->appendOutputTo($filePath);
        $schedule->command("load --services")->dailyAt("03:30")->appendOutputTo($filePath);
        $schedule->command("load --records --startDate={$date} --endDate={$date}")->dailyAt("03:50")->appendOutputTo($filePath);

        /* SOLVE */
        $schedule->command("solve --total-comission --date={$date}")->dailyAt("04:00")->appendOutputTo($filePath);
        $schedule->command("solve --masters-comission --date={$date}")->dailyAt("04:10")->appendOutputTo($filePath);
        $schedule->command("solve --masters-profit --date={$date}")->dailyAt("04:20")->appendOutputTo($filePath);
        $schedule->command("solve --custom-outcomes --date={$date}")->dailyAt("04:30")->appendOutputTo($filePath);
        $schedule->command("solve --managers-profit --date={$date}")->dailyAt("04:40")->appendOutputTo($filePath);
        $schedule->command("solve --operators-profit --date={$date}")->dailyAt("04:50")->appendOutputTo($filePath);
        $schedule->command("solve --masters-penalty --date={$date}")->dailyAt("04:55")->appendOutputTo($filePath);
    }

    private function solveLastWeek(string $lastWeekStart, string $lastWeekEnd, string $filePath, Schedule $schedule)
    {
        /* LOAD */
        $schedule->command("load --masters")->dailyAt("03:10")->appendOutputTo($filePath);
        $schedule->command("load --cosmetologists")->dailyAt("03:20")->appendOutputTo($filePath);
        $schedule->command("load --services")->dailyAt("03:30")->appendOutputTo($filePath);
        $schedule->command("load --records --startDate={$lastWeekStart} --endDate={$lastWeekEnd}")->dailyAt("03:50")->appendOutputTo($filePath);

        /* SOLVE */
        $schedule->command("solve --total-comission --startDate={$lastWeekStart} --endDate={$lastWeekEnd}")->dailyAt("04:00")->appendOutputTo($filePath);
        $schedule->command("solve --masters-comission --startDate={$lastWeekStart} --endDate={$lastWeekEnd}")->dailyAt("04:10")->appendOutputTo($filePath);
        $schedule->command("solve --masters-profit --startDate={$lastWeekStart} --endDate={$lastWeekEnd}")->dailyAt("04:20")->appendOutputTo($filePath);
        $schedule->command("solve --custom-outcomes --startDate={$lastWeekStart} --endDate={$lastWeekEnd}")->dailyAt("04:30")->appendOutputTo($filePath);
        $schedule->command("solve --managers-profit --startDate={$lastWeekStart} --endDate={$lastWeekEnd}")->dailyAt("04:40")->appendOutputTo($filePath);
        $schedule->command("solve --operators-profit --startDate={$lastWeekStart} --endDate={$lastWeekEnd}")->dailyAt("04:50")->appendOutputTo($filePath);
        $schedule->command("solve --masters-penalty --startDate={$lastWeekStart} --endDate={$lastWeekEnd}")->dailyAt("04:55")->appendOutputTo($filePath);
    }

    private function seedNewWeek(string $thisWeekStart, string $thisWeekEnd, string $filePath, Schedule $schedule)
    {
        /* SEED */
        $schedule->command("seed --contacts --startDate={$thisWeekStart} --endDate={$thisWeekEnd}")->dailyAt("02:00")->appendOutputTo($filePath);
        $schedule->command("seed --team-outcomes --startDate={$thisWeekStart} --endDate={$thisWeekEnd}")->dailyAt("02:10")->appendOutputTo($filePath);
        $schedule->command("seed --custom-outcomes --startDate={$thisWeekStart} --endDate={$thisWeekEnd}")->dailyAt("02:20")->appendOutputTo($filePath);
    }
}
