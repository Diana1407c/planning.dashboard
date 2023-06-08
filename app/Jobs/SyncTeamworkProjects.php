<?php

namespace App\Jobs;

use App\Services\Teamwork\TeamworkService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncTeamworkProjects implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 5;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('teamwork');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        TeamworkService::syncProjects();
    }
}
