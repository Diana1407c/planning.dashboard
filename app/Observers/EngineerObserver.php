<?php

namespace App\Observers;

use App\Models\Engineer;
use App\Repositories\EngineerHistoryRepository;

class EngineerObserver
{
    /**
     * Handle the Team "created" event.
     */
    public function created(Engineer $engineer): void
    {
        //
    }

    /**
     * Handle the Engineer "updated" event.
     */
    public function updated(Engineer $engineer): void
    {
        if ($engineer->isDirty('level_id')) {
            EngineerHistoryRepository::storeLevel($engineer);
        }

        if ($engineer->isDirty('performance')) {
            EngineerHistoryRepository::storePerformance($engineer);
        }

        if ($engineer->isDirty('team_id')) {
            EngineerHistoryRepository::storeTeam($engineer, $engineer->team);
        }
    }

    /**
     * Handle the Engineer "deleted" event.
     */
    public function deleted(Engineer $engineer): void
    {
        //
    }

    /**
     * Handle the Engineer "restored" event.
     */
    public function restored(Engineer $engineer): void
    {
        //
    }

    /**
     * Handle the Engineer "force deleted" event.
     */
    public function forceDeleted(Engineer $engineer): void
    {
        //
    }
}
