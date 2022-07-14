<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\MaintenanceSchedule::class,
        Commands\UpdateAttemptPayment::class,
        Commands\UpdatePendingPayment::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('maintenance:schedule')->hourly();
        $schedule->command('backup:clean')->daily()->at('00:30');
        $schedule->command('update:attempt')->hourly();
        $schedule->command('update:pending')->hourly();
        $schedule->command('backup:run --only-files')->daily()->at('00:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
