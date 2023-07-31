<?php

namespace App\Matrix;

use App\Models\Engineer;
use App\Models\PlannedHour;

class TLMonthlyPlannedHoursMatrix extends PlannedHoursMatrix
{
    protected $pmHours;

    public function matrix(): array
    {
        $this->tlHours = $this->plannedHourService()->hoursByFilter($this->filter->params);

        $pmFilter = $this->filter->clone()->set('planable_type', PlannedHour::TECHNOLOGY_TYPE);
        $this->pmHours = $this->plannedHourService()->hoursByFilter($pmFilter->params);

        return [
            'engineers' => $this->engineersHours(),
            'technologies' => $this->technologiesHours(),
        ];
    }

    protected function engineersGroupedByTechnology()
    {
        $query = Engineer::query()->select(['team_technology.technology_id', 'engineers.id'])
            ->join('teams', 'teams.id', '=', 'engineers.team_id')
            ->join('team_technology', 'teams.id', '=', 'team_technology.team_id');

        if ($teamIds = $this->filter->get('team_ids')) {
            $query->whereIn('teams.id', $teamIds);
        }

        return $query->get()->groupBy('technology_id');
    }

    protected function technologiesHours(): array
    {
        $engineers = $this->engineersGroupedByTechnology();

        $data = [];
        foreach ($engineers as $technologyId => $engineerIds) {
            foreach ($this->projects as $project) {
                $data[$technologyId][$project->id] = [
                    'planned_tl' => $this->tlHours->where('project_id', $project->id)
                        ->whereIn('planable_id', $engineerIds->pluck('id'))
                        ->sum('performance_hours'),
                    'planned_pm' => $this->pmHours->where('project_id', $project->id)
                        ->where('planable_id', $technologyId)
                        ->sum('hours'),
                ];
            }
            $data[$technologyId]['total'] = [
                'planned_tl' => $this->tlHours->whereIn('planable_id', $engineerIds->pluck('id'))
                    ->sum('performance_hours'),
                'planned_pm' => $this->pmHours->where('planable_id', $technologyId)->sum('hours')
            ];
        }

        return $data;
    }
}
