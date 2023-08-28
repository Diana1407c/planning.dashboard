<?php

namespace App\Support\Interval;

class DateFrom extends DateBase
{
    public function setPeriodExtreme(): void
    {
        if ($this->isWeek()) {
            $this->date->startOfWeek()->startOfDay();
            return;
        }
        $this->date->startOfMonth()->startOfDay();
    }
}
