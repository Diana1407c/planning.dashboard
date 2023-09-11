<?php

namespace App\Matrix;

class TLMonthlyPlannedHoursMatrix extends TLBaseMatrix
{
    public function matrix(): array
    {
        $this->setHours();
        $this->setTechnologyData();
        $this->setEngineersData();
        $this->setProjectData();

        return $this->data;
    }
}
