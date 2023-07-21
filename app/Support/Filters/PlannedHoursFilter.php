<?php

namespace App\Support\Filters;

use App\Support\GenericPeriod;

class PlannedHoursFilter
{
    public GenericPeriod $period;

    public function __construct(public array $params)
    {
        $this->setPeriod();
    }

    public static function fromArray(array $params): PlannedHoursFilter
    {
        return new self($params);
    }

    public function get(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function set(string $key, $value): PlannedHoursFilter
    {
        $this->params[$key] = $value;

        return $this;
    }

    public function setPeriod(): void
    {
        $this->period = GenericPeriod::fromType($this->get('period_type'), $this->get('year'), $this->get('period_number'));
    }

    public function clone(): PlannedHoursFilter
    {
        $clone = clone $this;
        $clone->period = $this->period->clone();

        return $clone;
    }
}
