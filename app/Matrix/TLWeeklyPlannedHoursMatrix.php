<?php

namespace App\Matrix;

class TLWeeklyPlannedHoursMatrix extends PlannedHoursMatrix
{

    public static function init(array $filter): PlannedHoursMatrix
    {
        $instance = new self($filter);
        $instance->initProjects();

        return $instance;
    }
    public function matrix(): array
    {
        $this->tlHours = $this->plannedHourService()->hoursByFilter($this->filter);

        return $this->engineersHours();
    }
}
