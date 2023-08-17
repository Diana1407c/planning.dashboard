<?php

namespace App\Matrix;

use App\Models\PlannedHour;
use App\Models\Technology;
use App\Services\Teamwork\TeamworkService;

class PMWeeklyPlannedHoursMatrix extends PlannedHoursMatrix
{
    protected $pmHours;

    public function matrix(): array
    {
        $this->pmHours = $this->plannedHourService()->hoursByFilter($this->filter->params);

        $tmHours = TeamworkService::technologiesHours([
            'projects_ids' => $this->projects->pluck('id'),
            'from_date' => $this->filter->period->from->clone()->startOfMonth(),
            'to_date' => $this->filter->period->from->clone()->endOfMonth(),
        ]);

        $monthlyFilter = $this->filter->clone()->set('period_type', PlannedHour::MONTH_PERIOD_TYPE)
            ->set('period_number', $this->filter->period->from->month);
        $monthlyHours = $this->plannedHourService()->hoursByFilter($monthlyFilter->params);

        $technologies = Technology::all();

        $data = [];

        foreach ($this->projects as $project) {
            foreach ($technologies as $technology) {
                $twTime = $tmHours->where('project_id', $project->id)
                    ->where('technology_id', $technology->id)->sum('sum_hours');

                $data[$project->id][$technology->id] = [
                    'week' =>  $this->pmHours->where('project_id', $project->id)
                            ->where('planable_id', $technology->id)
                            ->first()->hours ?? 0,
                    'month' => $monthlyHours->where('project_id', $project->id)
                            ->where('planable_id', $technology->id)
                            ->first()->hours ?? 0,
                    'tm' => twHours($twTime),
                ];
            }
            $data[$project->id]['total'] = [
                'week' => $this->pmHours->where('project_id', $project->id)->sum('hours'),
                'month' => $monthlyHours->where('project_id', $project->id)->sum('hours'),
                'tm' => twHours($tmHours->where('project_id', $project->id)->sum('sum_hours')),

            ];
        }

        return $data;
    }
}
