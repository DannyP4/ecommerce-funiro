<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // send daily revenue report at 23:59 every day
        $schedule->command('report:daily-revenue')
                //->everyMinute() // log test
                ->dailyAt('23:59')
                ->withoutOverlapping()
                ->timezone('Asia/Ho_Chi_Minh')
                ->emailOutputOnFailure('longpham19112004@gmail.com') // take the error message and send it to this email
                ->sendOutputTo(storage_path('logs/daily_revenue.log')); // log file for daily revenue report
        
        // $schedule->command('inspire')->everyMinute()
        //         ->sendOutputTo(storage_path('logs/cronjob.log')); // cronjob log test
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
