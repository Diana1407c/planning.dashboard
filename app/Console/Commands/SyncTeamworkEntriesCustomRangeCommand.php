<?php

namespace App\Console\Commands;

use App\Services\Teamwork\TeamworkService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncTeamworkEntriesCustomRangeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:sync-teamwork-custom {start} {end}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Teamwork entries based on start date and end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $start = Carbon::parse($this->argument('start'));
            $end = Carbon::parse($this->argument('end'));
        } catch (\Exception $exception){
            $this->comment('Could not parse the dates to Carbon date');
            return;
        }

        TeamworkService::syncTimeEntries($start, $end);
    }
}
