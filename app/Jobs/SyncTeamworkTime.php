<?php

namespace App\Jobs;

use App\Services\Teamwork\TeamworkService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncTeamworkTime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 5;

    public Carbon $startDate;
    public Carbon $endDate;

    /**
     * Create a new job instance.
     */
    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->onQueue('teamwork');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        TeamworkService::syncTimeEntries($this->startDate, $this->endDate);
    }
}
