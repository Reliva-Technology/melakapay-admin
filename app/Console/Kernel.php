<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\MaintenanceSchedule::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('maintenance:schedule');
        $schedule->command('backup:clean')->daily()->at('00:30')->evenInMaintenanceMode();
        $schedule->command('backup:run')->daily()->at('00:00')->evenInMaintenanceMode();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
