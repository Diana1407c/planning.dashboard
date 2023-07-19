<?php

namespace App\Matrix;

class TLWeeklyPlannedHoursMatrix extends PlannedHoursMatrix
{
    public function matrix(): array
    {
        $this->tlHours = $this->plannedHourService()->hoursByFilter($this->filter);

        return $this->engineersHours();
    }
}
