<?php

namespace App\Matrix;

use App\Models\Project;
use App\Services\EngineerService;
use App\Support\Filters\PlannedHoursFilter;

class AccountantMatrix extends PlannedHoursMatrix
{
    public function __construct(PlannedHoursFilter $filter, protected int $countHours)
    {
        parent::__construct($filter);
    }

    public function matrix(): array
    {
        $this->tlHours = $this->plannedHourService()->hoursByFilter($this->filter->params);


        $engineers = EngineerService::filter(['team_ids' => $this->filter->get('team_ids')]);

        $projectsTotal = 0;
        foreach (Project::states() as $state) {
            $totalState = 0;
            $projects = $this->projects->where('state', $state);
            foreach ($projects as $project) {
                $projectHours = $this->tlHours->where('project_id', $project->id)->sum('hours');
                $data['projects'][$project->id] = $projectHours;
                $totalState += $projectHours;
            }
            $data['projects'][$state] = $totalState;
            $projectsTotal += $totalState;
        }

        $data['projects']['all'] = $projectsTotal;

        $totalUnplanned = 0;
        foreach ($engineers as $engineer) {
            $totalEngineer = 0;
            foreach (Project::states() as $state) {
                $projects = $this->projects->where('state', $state);
                $totalEngineerState = 0;
                foreach ($projects as $project) {
                    $engineerHours = $this->tlHours->where('project_id', $project->id)
                        ->where('planable_id', $engineer->id)
                        ->first()->hours ?? 0;
                    $data['engineers'][$engineer->id][$project->id] = $engineerHours;
                    $totalEngineerState += $engineerHours;
                }
                $data['engineers'][$engineer->id]['total'][$state] = $totalEngineerState;
                $totalEngineer += $totalEngineerState;
            }

            $data['engineers'][$engineer->id]['total']['all'] = $totalEngineer;

            $unplanned = $this->countHours - $totalEngineer;
            $data['engineers'][$engineer->id]['total']['unplanned'] = $unplanned;
            $totalUnplanned += $unplanned;
        }

        $data['projects']['unplanned'] = $totalUnplanned;

        return $data;
    }
}
