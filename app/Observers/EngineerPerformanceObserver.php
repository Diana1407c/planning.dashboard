<?php

namespace App\Observers;

use App\Models\EngineerPerformance;
use App\Services\PlannedHourService;
use Carbon\Carbon;

class EngineerPerformanceObserver
{
    /**
     * Handle the Team "created" event.
     */
    public function created(EngineerPerformance $engineerPerformance): void
    {
        $this->resetCurrentPerformance($engineerPerformance);
        $this->resetEngineerPerformanceHoursFrom($engineerPerformance);
    }

    /**
     * Handle the Engineer "updated" event.
     */
    public function updated(EngineerPerformance $engineerPerformance): void
    {
        $this->resetCurrentPerformance($engineerPerformance);

        if ($engineerPerformance->isDirty('from_date')) {
            foreach ($engineerPerformance->engineer->performances as $performance) {
                $this->resetCurrentPerformance($performance);
            }
            return;
        }

        $this->resetEngineerPerformanceHoursFrom($engineerPerformance);
    }

    /**
     * Handle the Engineer "deleted" event.
     */
    public function deleted(EngineerPerformance $engineerPerformance): void
    {
        $this->resetCurrentPerformance($engineerPerformance);

        $performance = $engineerPerformance->engineer->performances()
            ->where('from_date', '<', $engineerPerformance->from_date)
            ->orderBy('from_date', 'desc')->first();

        if ($performance) {
            $this->resetEngineerPerformanceHoursFrom($performance);

            return;
        }

        $nextPerformance = $engineerPerformance->engineer->performances()
            ->where('from_date', '>', $engineerPerformance->from_date)
            ->orderBy('from_date', 'asc')->first();

        $from = Carbon::parse($engineerPerformance->engineer->created_at);
        $to = $nextPerformance ? Carbon::parse($nextPerformance->from_date) : null;

        (new PlannedHourService())->resetEngineerPerformanceHours($engineerPerformance->engineer, 100, $from, $to);
    }

    /**
     * Handle the Engineer "restored" event.
     */
    public function restored(EngineerPerformance $engineerPerformance): void
    {
        //
    }

    /**
     * Handle the Engineer "force deleted" event.
     */
    public function forceDeleted(EngineerPerformance $engineerPerformance): void
    {
        //
    }

    protected function resetCurrentPerformance(EngineerPerformance $engineerPerformance): void
    {
        $engineer = $engineerPerformance->engineer;
        $project = $engineerPerformance->project;

        $currentPerformance = $engineer->performances()->where('project_id', $project->id)->orderBy('from_date', 'desc')->first();

        if (!$currentPerformance) {
            return;
        }

        $engineer->performances()->where('id', $currentPerformance->id)->update([
            'is_current' => true,
        ]);

        $engineer->performances()->where('id', '<>', $currentPerformance->id)->where('project_id', $project->id)->update([
            'is_current' => false,
        ]);
    }

    public function resetEngineerPerformanceHoursFrom(EngineerPerformance $engineerPerformance)
    {
        $percent = $engineerPerformance->performancePercent();

        $nextPerformance = $engineerPerformance->engineer->performances()
            ->where('from_date', '>', $engineerPerformance->from_date)
            ->orderBy('from_date', 'asc')->first();

        $from = Carbon::parse($engineerPerformance->from_date);
        $to = $nextPerformance ? Carbon::parse($nextPerformance->from_date) : null;

        (new PlannedHourService())->resetEngineerPerformanceHours($engineerPerformance->engineer, $percent, $from, $to);
    }
}
