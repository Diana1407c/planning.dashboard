<?php

namespace App\Matrix;

use App\Services\EngineerService;
use App\Services\PlannedHourService;
use App\Services\ProjectService;

class PlannedHoursMatrix
{
    protected $tlHours;

    protected $projects;
    protected $plannedHourService;

    public function __construct(protected array $filter)
    {
    }

    public function initProjects(): void
    {
        $this->projects = ProjectService::filter($this->filter);
    }

    protected function engineersHours(): array
    {
        $engineers = EngineerService::filter(['team_ids' => $this->filter['team_ids'] ?? null]);

        $data = [];
        foreach ($engineers as $engineer) {
            foreach ($this->projects as $project) {
                $data[$engineer->id][$project->id] =
                    $this->tlHours->where('project_id', $project->id)
                        ->where('planable_id', $engineer->id)
                        ->first()->hours ?? 0;
            }
            $data[$engineer->id]['total'] = $this->tlHours->where('planable_id', $engineer->id)->sum('hours');
        }

        return $data;
    }

    protected function plannedHourService(): PlannedHourService
    {
        if (!$this->plannedHourService) {
            $this->plannedHourService = new PlannedHourService();
        }

        return $this->plannedHourService;
    }
}
