<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * @var string|null
     */
    private $outputPath;

    /**
     * @param Application $app
     * @param Dispatcher $events
     */
    public function __construct(Application $app, Dispatcher $events)
    {
        parent::__construct($app, $events);
        $this->outputPath = storage_path('logs/cronjob.log');
    }

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('command:sync-teamwork current_week')->cron('30 18 * * *')->appendOutputTo($this->outputPath);
        $schedule->command('command:sync-teamwork current_week')->cron('0 9 * * *')->appendOutputTo($this->outputPath);
        $schedule->command('command:sync-teamwork current_week')->cron('0 1 * * 6')->appendOutputTo($this->outputPath);
        $schedule->command('command:sync-teamwork past_week')->cron('0 1 * * 1')->appendOutputTo($this->outputPath);

        $schedule->command('command:sync-teamwork past_month')->cron('0 1 1 * *')->appendOutputTo($this->outputPath);
        $schedule->command('email:send-discrepancy-report')->weekdays()->at('19:00')->appendOutputTo($this->outputPath);
        $schedule->command('command:sync-teamwork current_week')->fridays()->at('16:30')->appendOutputTo($this->outputPath);
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
