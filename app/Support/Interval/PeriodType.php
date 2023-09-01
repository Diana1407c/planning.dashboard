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

    public function toSqlFuction(string $column, $alias = null): string
    {
        $sqlFunction = $this->isWeek() ? "WEEK" : "MONTH";
        $sql = "$sqlFunction($column)";
        if ($alias) {
            return "$sql AS $alias";
        }
        return $sql;
    }
}


