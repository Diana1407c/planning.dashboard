<?php

namespace App\Services;

use App\Models\Holiday;
use App\Support\Interval\GenericInterval;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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

    public function weekWorkHours(Carbon $startOfWeek, Carbon $dayOfWeek)
    {
        $hours = $this->workHoursByPeriod($startOfWeek, $dayOfWeek);
        $holidays = $this->weekHolidays($startOfWeek, $dayOfWeek);


        foreach ($holidays as $holiday) {
            if ($dayOfWeek->gte($holiday->date)) {
                $hours += $holiday->hours();
            }
        }

        return $hours;
    }

    public function weekHolidays(Carbon $startOfWeek, Carbon $dayOfWeek): Collection
    {
        return Holiday::query()
            ->whereBetween('date', [$startOfWeek, $dayOfWeek])
            ->orWhere(function (Builder $query) use ($startOfWeek) {
                $query->where('every_year', true)
                    ->whereRaw('WEEK(date)=' . $startOfWeek->weekOfYear);
            })->get();
    }

    public function workHoursPerInterval(GenericInterval $interval)
    {
        $hours = $this->workHoursByPeriod($interval->from->date, $interval->to->date);
        $holidays = $this->holidaysPerInterval($interval);

        foreach ($holidays as $holiday) {
            $hours += $holiday->hours();
        }

        return $hours;
    }

    public function holidaysPerInterval(GenericInterval $interval)
    {
        return Holiday::query()
            ->whereBetween('date', [$interval->from->date, $interval->to->date])
            ->orWhere(function (Builder $query) use ($interval) {
                $this->queryFromPeriod($query, $interval);
                $this->queryToPeriod($query, $interval);
            })->get();
    }

    protected function queryFromPeriod($query, GenericInterval $interval): void
    {
        $query->where(function ($q) use ($interval) {
            $q->where(function ($qYear) use ($interval) {
                $qYear->where('date', '=', $interval->from->date->year)
                    ->whereRaw($interval->toSqlFuction("date") . '>=' . $interval->from->periodNumber());
            })->orWhere('date', '>', $interval->from->date->year);
        });
    }

    protected function queryToPeriod($query, GenericInterval $interval): void
    {
        $query->where(function ($q) use ($interval) {
            $q->where(function ($qYear) use ($interval) {
                $qYear->where('date', '=', $interval->to->date->year)
                    ->whereRaw($interval->toSqlFuction("date") . '<=' . $interval->from->periodNumber());
            })->orWhere('date', '<', $interval->to->date->year);
        });
    }

}
