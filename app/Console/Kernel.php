<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //

        \App\Console\Commands\SlackUserSync::class,
        \App\Console\Commands\RefreshStats::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {



        $filePath = storage_path('logs/slack_user_sync.log');



        $schedule->command('slack:usersync')
            ->daily()
            ->appendOutputTo($filePath);


    }
}
