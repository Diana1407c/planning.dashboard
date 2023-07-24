<?php

namespace App\Observers;

use App\Models\Engineer;
use App\Models\EngineerHistory;

class EngineerLevelPerformanceHistoryObserver
{
    /**
     * Handle the Engineer "created" event.
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
        $this->createLog($engineer, 'update');
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

    private function createLog(Engineer $engineer, $action)
    {
        $dirtyAttributes = $engineer->getDirty();
            if ($action === 'update') {
                if (array_key_exists('level_id', $dirtyAttributes)) {
                    EngineerHistory::create([
                        'engineer_id' => $engineer->id,
                        'historyable_type' => 'level',
                        'historyable_id' => $dirtyAttributes['level_id'],
                        'value' => $dirtyAttributes['level_id'] ? $engineer->level->name : null,
                        $value = $dirtyAttributes['level_id'] ? $engineer->level->name : null,
                        'label' => $value ? 'engineer_level_changed_to' : 'engineer_level_deleted',
                    ]);
                }
                if (array_key_exists('performance', $dirtyAttributes)) {
                EngineerHistory::create([
                    'engineer_id' => $engineer->id,
                    'historyable_type' => 'performance',
                    'historyable_id' => $dirtyAttributes['performance'],
                    'value' => $dirtyAttributes['performance'] ? $dirtyAttributes['performance'] : null,
                    $value = $dirtyAttributes['performance'] ? $dirtyAttributes['performance'] : null,
                    'label' => $value ? 'engineer_performance_changed_to' : 'engineer_performance_deleted',
                ]);
            }
        }
    }
}
