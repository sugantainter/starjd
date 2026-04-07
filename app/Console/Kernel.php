<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SendUserReminders;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Run the reminder command daily at 02:00 AM
        $schedule->command('app:send-user-reminders')->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        // You may also manually register commands here
        // $this->command(SendUserReminders::class);
    }
}
?>
