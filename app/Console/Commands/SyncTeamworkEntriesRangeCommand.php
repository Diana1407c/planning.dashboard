<?php

namespace App\Console\Commands;

use App\Jobs\SyncTeamworkTime;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncTeamworkEntriesRangeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-teamwork {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Teamwork entries based on fixed range parameter given';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        switch ($this->argument('type'))
        {
            case 'current_month':
                $fromDate = Carbon::now()->startOfMonth();
                $toDate = Carbon::now()->endOfMonth();
                break;
            case 'past_month':
                $fromDate = Carbon::now()->subMonth()->startOfMonth();
                $toDate = Carbon::now()->subMonth()->endOfWeek();
                break;
            case 'current_week':
                $fromDate = Carbon::now()->startOfWeek();
                $toDate = Carbon::now()->endOfWeek();
                break;
            default:
                $fromDate = Carbon::now()->subWeek()->startOfWeek();
                $toDate = Carbon::now()->subWeek()->endOfWeek();
        }

        SyncTeamworkTime::dispatch($fromDate, $toDate);
    }
}
