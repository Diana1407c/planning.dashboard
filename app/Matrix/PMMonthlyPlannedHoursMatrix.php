<?php

namespace App\Matrix;

use App\Models\Technology;

class PMMonthlyPlannedHoursMatrix extends PlannedHoursMatrix
{
    protected $pmHours;

    public function matrix(): array
    {
        $this->pmHours = $this->plannedHourService()->hoursByFilter($this->filter->params);

        $technologies = Technology::all();

        $data = [];

        foreach ($this->projects as $project) {
            foreach ($technologies as $technology) {
                $data[$project->id][$technology->id] =
                    $this->pmHours->where('project_id', $project->id)
                        ->where('planable_id', $technology->id)
                        ->first()->hours ?? 0;
            }
            $data[$project->id]['total'] = $this->pmHours->where('project_id', $project->id)->sum('hours');
        }

        return $data;
    }
}
