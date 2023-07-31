<?php

namespace App\Observers;

use App\Models\EngineerPerformance;

class EngineerPerformanceObserver
{
    /**
     * Handle the Team "created" event.
     */
    public function created(EngineerPerformance $engineerPerformance): void
    {
        $this->resetCurrentPerformance($engineerPerformance);
    }

    /**
     * Handle the Engineer "updated" event.
     */
    public function updated(EngineerPerformance $engineerPerformance): void
    {
        $this->resetCurrentPerformance($engineerPerformance);
    }

    /**
     * Handle the Engineer "deleted" event.
     */
    public function deleted(EngineerPerformance $engineerPerformance): void
    {
        $this->resetCurrentPerformance($engineerPerformance);
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

        $currentPerformance = $engineer->performances()->orderBy('from_date', 'desc')->first();

        if (!$currentPerformance) {
            return;
        }

        $engineer->performances()->where('id', $currentPerformance->id)->update([
            'is_current' => true,
        ]);

        $engineer->performances()->where('id', '<>', $currentPerformance->id)->update([
            'is_current' => false,
        ]);
    }
}
