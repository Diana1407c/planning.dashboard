<?php

namespace App\Support\Interval;

use Carbon\Carbon;

abstract class DateBase extends PeriodType
{
    public function __construct(public Carbon $date, string $type)
    {
        parent::__construct($type);
        $this->setPeriodExtreme();
    }

    abstract public function setPeriodExtreme();

    public function periodNumber(): int
    {
        if ($this->isWeek()) {
            return $this->date->week;
        }
        return $this->date->month;
    }
}


