<?php

namespace App\Support\Interval;


class Period extends DateBase
{
    public function setPeriodExtreme(): void
    {
    }

    public function toLabel(): string
    {
        if ($this->isWeek()) {
            return "{$this->date->format('d.m.Y')} - {$this->date->clone()->endOfWeek()->format('d.m.Y')}";
        }
        return "{$this->date->format('M Y')}";
    }
}
