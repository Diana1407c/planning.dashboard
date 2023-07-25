<?php

namespace App\Observers;

use App\Http\Controllers\Admin\TeamCrudController;
use App\Http\Requests\TeamRequest;
use App\Models\Engineer;
use App\Models\EngineerHistory;
use App\Models\Team;

class EngineerTeamHistoryObserver
{
    /**
     * Handle the Team "created" event.
     */
    public function created(Team $team): void
    {
        $this->createLog($team, 'create');
    }

    /**
     * Handle the Team "updated" event.
     */
    public function updated(Team $team): void
    {
        $this->createLog($team, 'update');
    }

    /**
     * Handle the Team "deleted" event.
     */
    public function deleting(Team $team): void
    {
        $this->createLog($team, 'delete');
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
    private function createLog(Team $team, $action)
    {
        $dirtyAttributes = $team->getDirty();

        if ($action === 'update') {
            if (array_key_exists('team_lead_id', $dirtyAttributes)) {
                $oldTeamLeadId = $team->getOriginal('team_lead_id');
                if ($oldTeamLeadId && $oldTeamLeadId !== $team->team_lead_id) {
                    EngineerHistory::create([
                        'engineer_id' => $oldTeamLeadId,
                        'historyable_type' => 'team',
                        'historyable_id' => $team->id,
                        'value' => $team->name,
                        'label' => 'engineer_removed_as_team_lead_id_from',
                    ]);
                }
                EngineerHistory::create([
                    'engineer_id' => $team->team_lead_id,
                    'historyable_type' => 'team',
                    'historyable_id' => $team->id,
                    'value' => $team->name,
                    'label' => 'engineer_added_as_team_lead_id_to',
                ]);
            }
        } elseif ($action === 'create') {
            $membersIds = $team->members->pluck('id')->toArray();
            if (!in_array($team->team_lead_id, $membersIds)) {
                $membersIds[] = $team->team_lead_id;
            }
            Engineer::query()->whereIn('id', $membersIds)->update(['team_id' => $team->id]);

            foreach ($membersIds as $memberId) {
                if ($memberId) {
                    EngineerHistory::create([
                        'engineer_id' => $memberId,
                        'historyable_type' => 'team',
                        'historyable_id' => $team->id,
                        'value' => $team->name,
                        'label' => 'engineer_added_to_team',
                    ]);
                }
            }
        }elseif ($action === 'delete') {
            $membersIds = $team->members->pluck('id')->toArray();
            Engineer::query()->whereIn('id', $membersIds)->update(['team_id' => null]);

            foreach ($membersIds as $memberId) {
                EngineerHistory::create([
                    'engineer_id' => $memberId,
                    'historyable_type' => 'team',
                    'historyable_id' => $team->id,
                    'value' => $team->name,
                    'label' => 'engineer_deleted_from_team',
                ]);
            }

        }
    }
}
