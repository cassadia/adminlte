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
        // $schedule->command('scheduler:command postTransaction')->everyMinute();
        $schedule->command('scheduler:command getListItemNew')->everyTenMinutes();
        // $schedule->command('scheduler:command updatePriceAndStockNew')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('scheduler:command getSession')->weekly()->mondays()->at('23:00');
        $schedule->command('scheduler:command refreshToken')->weekly()->mondays()->at('23:30');

        $schedule->command('logs:clear')->dailyAt('00:00');

        // Panggil getListitem setiap jam 2 pagi
        // $schedule->command('scheduler:command getListitem')->dailyAt('2:00');

        // Panggil postTransaction setiap hari Senin pukul 10 pagi
        // $schedule->command('scheduler:command postTransaction')->mondays()->at('10:00');

        // Panggil refreshToken setiap hari Rabu pukul 4 sore
        // $schedule->command('scheduler:command refreshToken')->wednesdays()->at('16:00');
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
