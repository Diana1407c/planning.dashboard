<?php

namespace App\Support\Interval;

class DateTo extends DateBase
{
    public function setPeriodExtreme(): void
    {
        if ($this->isWeek()) {
            $this->date->endOfWeek()->endOfDay();
            return;
        }
        $this->date->endOfMonth()->endOfDay();
    }
}
