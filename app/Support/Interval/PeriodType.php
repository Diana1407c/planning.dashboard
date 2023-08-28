<?php

namespace App\Support\Interval;

use App\Models\PlannedHour;

class PeriodType
{
    public function __construct(public string $type)
    {
    }

    public function isWeek(): bool
    {
        return $this->type == PlannedHour::WEEK_PERIOD_TYPE;
    }

    public function toSql(): string
    {
        if ($this->isWeek()) {
            return "WEEK(date) AS period_number";
        }
        return "MONTH(date) AS period_number";
    }
}


