<?php

namespace App\Matrix;

use App\Services\PlannedHourService;
use App\Services\ProjectService;
use App\Support\Filters\PlannedHoursFilter;

class PlannedHoursMatrix
{
    protected $tlHours;

    protected $projects;
    protected $plannedHourService;

    public function __construct(protected PlannedHoursFilter $filter)
    {
        $this->initProjects();
    }

    public function initProjects(): void
    {
        $this->projects = ProjectService::filter($this->filter->params);
    }

    protected function plannedHourService(): PlannedHourService
    {
        if (!$this->plannedHourService) {
            $this->plannedHourService = new PlannedHourService();
        }

        return $this->plannedHourService;
    }
}
