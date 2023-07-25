<?php

namespace App\Observers;

use App\Models\Team;
use App\Repositories\EngineerHistoryRepository;

class TeamObserver
{
    /**
     * Handle the Team "created" event.
     */
    public function created(Team $team): void
    {
        //
    }

    /**
     * Handle the Team "updated" event.
     */
    public function updated(Team $team): void
    {
        //
    }

    /**
     * Handle the Team "deleted" event.
     */
    public function deleting(Team $team): void
    {
        foreach ($team->members as $member) {
            EngineerHistoryRepository::storeTeam($member, $team, 'engineer_deleted_from_team');
        }
    }

    /**
     * Handle the Team "restored" event.
     */
    public function restored(Team $team): void
    {
        //
    }

    /**
     * Handle the Team "force deleted" event.
     */
    public function forceDeleted(Team $team): void
    {
        //
    }
}
