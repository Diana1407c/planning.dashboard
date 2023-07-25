<?php

namespace App\Services;

use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class HolidayService
{
    public function monthWorkHours(Carbon $startOfMonth, Carbon $endOfMonth)
    {
        $hours = $this->workHoursByPeriod($startOfMonth, $endOfMonth);
        $holidays = $this->monthHolidays($startOfMonth, $endOfMonth);

        foreach ($holidays as $holiday) {
            $hours += $holiday->hours();
        }

        return $hours;
    }

    public function monthHolidays(Carbon $startOfMonth, Carbon $endOfMonth)
    {
        return Holiday::query()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->orWhere(function (Builder $query) use ($startOfMonth) {
                $query->where('every_year', true)
                    ->whereRaw('MONTH(date)=' . $startOfMonth->month);
            })->get();
    }

    public function workHoursByPeriod(Carbon $from, Carbon $to): int
    {
        $days = 0;

        for ($i = $from->clone(); $i->lte($to); $i->addDay()) {
            if ($i->isWeekday()) {
                $days++;
            }
        }

        return $days * Holiday::DAY_HOURS;
    }
}
