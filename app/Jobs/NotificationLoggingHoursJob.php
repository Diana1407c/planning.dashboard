<?php

namespace App\Jobs;

use App\Mail\IncompleteWorkedHoursMail;
use App\Models\Engineer;
use App\Models\TeamworkTime;
use App\Models\User;
use App\Services\HolidayService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotificationLoggingHoursJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(HolidayService $holidayService): void
    {
        $engineerIds = Engineer::query()->whereNotNull('team_id')->pluck('id');
        $from = Carbon::now()->startOfWeek()->startOfDay();
        $to = Carbon::now();

        $requiredHours = $holidayService->weekWorkHours($from, $to);

        $whiteList = TeamworkTime::query()
            ->whereIn('engineer_id',  $engineerIds)
            ->whereBetween('date', [$from, $to])
            ->havingRaw('SUM(hours) >= '.$requiredHours)
            ->groupBy('engineer_id')->pluck('engineer_id');

        $engineerIds = $engineerIds->diff($whiteList);

        if ($engineerIds->isEmpty()) {
            return;
        }

        $engineers = Engineer::query()->whereIn('id', $engineerIds)->with('team')
            ->orderBy('team_id', 'asc')->get();

        $hours = TeamworkTime::query()
            ->select(['engineer_id', 'date'])
            ->selectRaw('SUM(hours) as hours')
            ->whereIn('engineer_id',  $engineerIds)
            ->whereBetween('date', [$from, $to])
            ->groupBy('engineer_id', 'date')->get()->groupBy(['engineer_id']);

        $emails = User::query()->pluck('email')->toArray();

        $dates = [];
        foreach (CarbonPeriod::create($from, $to) as $date) {
            $dates[] = $date;
        }

        $data = (object)[
            'engineers' => $engineers,
            'hours' => $hours,
            'dates' => $dates,
            'requiredHours' => $requiredHours,
        ];

        Mail::to($emails)->send(new IncompleteWorkedHoursMail($data));
    }
}
