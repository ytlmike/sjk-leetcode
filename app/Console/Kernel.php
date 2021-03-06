<?php

namespace App\Console;

use App\Console\Commands\JudgeUser;
use App\Console\Commands\SyncLeetcodeData;
use App\Console\Commands\SyncUserQuestion;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command((new SyncLeetcodeData())->getName())->daily();
        $schedule->command((new SyncUserQuestion())->getName())->daily();
        $schedule->command((new JudgeUser())->getName())->weeklyOn('1');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
