<?php

namespace App\Support\Interval;

use App\Models\PlannedHour;
use Carbon\Carbon;

class GenericInterval extends PeriodType
{
    public DateFrom $from;
    public DateTo $to;

    public function __construct(string $type, Carbon $from, Carbon $to)
    {
        parent::__construct($type);
        $this->from = new DateFrom($from, $type);
        $this->to = new DateTo($to, $type);
    }

    public static function fromString(string $type, string $from, string $to = null): GenericInterval
    {
        $from = Carbon::parse($from);
        $to = $to ? Carbon::parse($to) : $from->clone()->addMonth();

        return new self($type, $from, $to);
    }

    public static function fromCarbon(string $type, Carbon $from, Carbon $to = null): GenericInterval
    {
        if (!$to) {
            $to = $from->clone()->addMonth();
        }
        return new self($type, $from, $to);
    }

    public function isWeek(): bool
    {
        return $this->type == PlannedHour::WEEK_PERIOD_TYPE;
    }

    public function weekPeriods()
    {
        $periods = [];

        for ($date = $this->from->date->copy(); $date->lte($this->to->date); $date->addWeek()) {
            $weekStart = $date->copy()->startOfWeek();

            $periods[] = new Period($weekStart, $this->type);
        }

        return $periods;
    }

    public function monthPeriods()
    {
        $periods = [];

        for ($date = $this->from->date->copy(); $date->lte($this->to->date); $date->addMonth()) {
            $monthStart = $date->copy()->startOfMonth();

            $periods[] = new Period($monthStart, $this->type);
        }

        return $periods;
    }

    public function getPeriods()
    {
        if ($this->isWeek()) {
            return $this->weekPeriods();
        } else {
            return $this->monthPeriods();
        }
    }
}
