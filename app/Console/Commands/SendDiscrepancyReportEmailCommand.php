<?php

namespace App\Console\Commands;

use App\Jobs\NotificationLoggingHoursJob;
use Illuminate\Console\Command;

class SendDiscrepancyReportEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-discrepancy-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email with list of engineers that have incomplete worked hours';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        NotificationLoggingHoursJob::dispatch();
    }
}
