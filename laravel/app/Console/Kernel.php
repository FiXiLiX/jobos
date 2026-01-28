<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('budgets:ensure')->weekly();
        $schedule->job(new \App\Jobs\FetchDailyExchangeRates())->dailyAt('00:15');
        
        // Daily database export at 2 AM
        $schedule->command('database:export --compress')->dailyAt('02:00')->description('Daily database export');
    }
}
