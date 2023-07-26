<?php

namespace App\Matrix;

use App\Models\Engineer;
use App\Models\PlannedHour;
use App\Services\EngineerService;
use App\Services\Teamwork\TeamworkService;

class TLWeeklyPlannedHoursMatrix extends PlannedHoursMatrix
{
    public function matrix(): array
    {
        $this->tlHours = $this->plannedHourService()->hoursByFilterWithPerformance($this->filter->params);

        return [
            'engineers' => $this->engineersHours(),
            'technologies' => $this->technologiesHours(),
        ];
    }

    protected function engineersHours(): array
    {
        $engineers = EngineerService::filter(['team_ids' => $this->filter->get('team_ids')]);

        $filterPrevWeek = $this->filter->clone();
        $filterPrevWeek->period->from->subWeek();
        $filterPrevWeek->period->to->subWeek();
        $filterPrevWeek->set('year', $filterPrevWeek->period->from->year)
            ->set('period_number', $filterPrevWeek->period->from->week);
        $tlHoursPrevWeek = $this->plannedHourService()->hoursByFilter($filterPrevWeek->params);

        $twHours = TeamworkService::engineersHours([
            'projects_ids' => $this->projects->pluck('id'),
            'from_date' => $filterPrevWeek->period->from,
            'to_date' => $filterPrevWeek->period->to,
        ]);

        $data = [];
        foreach ($engineers as $engineer) {
            foreach ($this->projects as $project) {
                $twTime = $twHours->where('project_id', $project->id)
                    ->where('engineer_id', $engineer->id)->sum('sum_hours');

                $data[$engineer->id][$project->id] = [
                    'current_week' => $this->tlHours->where('project_id', $project->id)
                            ->where('planable_id', $engineer->id)
                            ->first()->hours ?? 0,
                    'prev_week' => $tlHoursPrevWeek->where('project_id', $project->id)
                            ->where('planable_id', $engineer->id)
                            ->first()->hours ?? 0,
                    'tw_prev_week' => round($twTime, 2),
                ];
            }
            $data[$engineer->id]['total'] = $this->tlHours->where('planable_id', $engineer->id)->sum('hours');
        }

        return $data;
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
        $pmFilter = $this->filter->clone()->set('planable_type', PlannedHour::TECHNOLOGY_TYPE);
        $pmWeekHours = $this->plannedHourService()->hoursByFilter($pmFilter->params);

        $pmFilter->period->from->startOfMonth();
        $pmFilter->period->to = $pmFilter->period->from->clone()->endOfMonth();
        $pmFilter->set('year', $pmFilter->period->from->year)
            ->set('period_number', $pmFilter->period->from->month)
            ->set('period_type', PlannedHour::MONTH_PERIOD_TYPE);

        $pmMonthHours = $this->plannedHourService()->hoursByFilter($pmFilter->params);

        $twMonthHours = TeamworkService::technologiesHours([
            'projects_ids' => $this->projects->pluck('id'),
            'from_date' => $pmFilter->period->from,
            'to_date' => $pmFilter->period->to,
        ]);

        $engineers = $this->engineersGroupedByTechnology();

        $data = [];
        foreach ($engineers as $technologyId => $engineerIds) {
            foreach ($this->projects as $project) {
                $twTime = $twMonthHours->where('project_id', $project->id)
                    ->where('technology_id', $technologyId)->sum('sum_hours');

                $data[$technologyId][$project->id] = [
                    'tl_week' => $this->tlHours->where('project_id', $project->id)
                        ->whereIn('planable_id', $engineerIds->pluck('id'))
                        ->sum('real_hours'),
                    'pm_week' => $pmWeekHours->where('project_id', $project->id)
                        ->where('planable_id', $technologyId)
                        ->sum('hours'),
                    'pm_month' => $pmMonthHours->where('project_id', $project->id)
                        ->where('planable_id', $technologyId)
                        ->sum('hours'),
                    'tw_month' => round($twTime, 2),
                ];
            }
            $data[$technologyId]['total'] = [
                'tl_week' => $this->tlHours->whereIn('planable_id', $engineerIds->pluck('id'))->sum('real_hours'),
                'pm_week' => $pmWeekHours->where('planable_id', $technologyId)->sum('hours'),
                'pm_month' => $pmMonthHours->where('planable_id', $technologyId)->sum('hours'),
                'tw_month' => round($twMonthHours->where('technology_id', $technologyId)->sum('sum_hours'), 2),
            ];
        }

        return $data;
    }
}
