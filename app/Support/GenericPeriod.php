<?php

namespace App\Support;

use App\Models\PlannedHour;
use Carbon\Carbon;

class GenericPeriod
{
    public Carbon $from;
    public Carbon $to;

    public function __construct(public string $type, public int $year, public int $number)
    {
        $this->setInterval();
    }

    public static function fromType(string $type, string $year, int $number): GenericPeriod
    {
        return new self($type, $year, $number);
    }

    public function isWeek(): bool
    {
        return $this->type == PlannedHour::WEEK_PERIOD_TYPE;
    }

    public function clone(): GenericPeriod
    {
        $clone = clone $this;

        $clone->from = $this->from->clone();
        $clone->to = $this->to->clone();

        return $clone;
    }

    protected function setInterval(): void
    {
        $this->from = Carbon::now();
        if ($this->isWeek()) {
            $this->from->setISODate($this->year, $this->number)->startOfWeek();
            $this->to = $this->from->clone()->endOfWeek();
            return;
        }

        $this->from->setYear($this->year)->setMonth($this->number)->startOfMonth();
        $this->to = $this->from->clone()->endOfMonth();
    }
}
