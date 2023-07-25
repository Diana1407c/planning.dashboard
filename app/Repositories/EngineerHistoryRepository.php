<?php

namespace App\Repositories;

use App\Models\Engineer;
use App\Models\EngineerHistory;
use App\Models\Team;


class EngineerHistoryRepository
{
    public static function storePerformance(Engineer $engineer): void
    {
        EngineerHistory::create([
            'engineer_id' => $engineer->id,
            'historyable_type' => 'performance',
            'historyable_id' => null,
            'value' => $engineer->performance,
            'label' => $engineer->performance ? 'engineer_performance_changed_to' : 'engineer_performance_deleted',
        ]);
    }

    public static function storeLevel(Engineer $engineer): void
    {
        EngineerHistory::create([
            'engineer_id' => $engineer->id,
            'historyable_type' => 'level',
            'historyable_id' => $engineer->level_id,
            'value' => $engineer->level_id ? $engineer->level->name : null,
            'label' => $engineer->level_id ? 'engineer_level_changed_to' : 'engineer_level_deleted',
        ]);
    }

    public static function storeTeam(Engineer $engineer, Team $team = null, string $label = 'engineer_added_to_team'): void
    {
        EngineerHistory::create([
            'engineer_id' => $engineer->id,
            'historyable_type' => 'team',
            'historyable_id' => $engineer->team_id,
            'value' => $team?->name,
            'label' => $label,
        ]);
    }
}
