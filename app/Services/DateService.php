<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class DateService
{
    public static function toWeek($date): array
    {
        $carbonDate = Carbon::parse($date);

        return !$carbonDate ? [] : [
            'week' => $carbonDate->weekOfYear,
            'year' => $carbonDate->year,
        ];
    }

    public static function toMonth($date): array
    {
        $carbonDate = Carbon::parse($date);

        return !$carbonDate ? [] : [
            'month' => $carbonDate->month,
            'year' => $carbonDate->year,
        ];
    }

    public static function rangeToMonths($startDate, $endDate = null): array
    {
        if($endDate){
            $carbonEndDate = Carbon::parse($endDate);
        } else {
            return self::toMonth($startDate);
        }

        $carbonStartMonth = self::toMonth($startDate);

        return [
            'start_month' => $carbonStartMonth['month'],
            'start_year' => $carbonStartMonth['year'],
            'end_month' => $carbonEndDate->month,
            'end_year' => $carbonEndDate->year,
        ];
    }

    public static function monthsDateArray($dates): array
    {
        $yearMonthArray = [];

        if(isset($dates['year']) && isset($dates['month'])){
            $yearMonthArray[] = [
                'month' => $dates['month'],
                'year' => $dates['year'],
                'index' => $dates['year'].'_'.$dates['month'],
                'formatted' => Carbon::createFromDate($dates['year'], $dates['month'], 1)->format('F Y'),
            ];

            return $yearMonthArray;
        }

        $startDate = Carbon::createFromDate($dates['start_year'], $dates['start_month'], 1)->startOfMonth();
        $endDate = Carbon::createFromDate($dates['end_year'], $dates['end_month'], 1)->endOfMonth();

        $currentDate = $startDate;
        while ($currentDate <= $endDate) {
            $month = $currentDate->month;
            $year = $currentDate->year;

            $index = $year . '_' . $month;
            $formatted = $currentDate->format('F Y');

            $yearMonthArray[] = [
                'month' => $month,
                'year' => $year,
                'index' => $index,
                'formatted' => $formatted,
            ];

            $currentDate->addMonth();
        }

        return $yearMonthArray;
    }

    public static function rangeToWeeks($startDate, $endDate = null): array
    {
        if($endDate){
            $carbonEndDate = Carbon::parse($endDate);
        } else {
            $carbonEndDate = Carbon::parse($startDate)->addMonth();
        }

        $carbonStartWeek = self::toWeek($startDate);

        return [
            'start_week' => $carbonStartWeek['week'],
            'start_year' => $carbonStartWeek['year'],
            'end_week' => $carbonEndDate->weekOfYear,
            'end_year' => $carbonEndDate->year,
        ];
    }

    public static function weeksDateArray($dates): array
    {
        $yearWeekArray = [];

        $startDate = Carbon::now()->setISODate($dates['start_year'], $dates['start_week'])->startOfWeek();
        $endDate = Carbon::now()->setISODate($dates['end_year'], $dates['end_week'])->startOfWeek();

        while ($startDate->lte($endDate)) {
            $startWeek = clone $startDate;
            $endWeek = (clone $startDate)->addDays(6);

            $year = $startWeek->format('Y');
            $week = $startWeek->format('W');
            $index = $year.'_'.$week;

            $startFormatted = $startWeek->format('j');
            $endFormatted = $endWeek->format('j M Y');
            if(!$startWeek->isSameMonth($endWeek)){
                $startFormatted .= $startWeek->format(' M');
            }

            if(!$startWeek->isSameYear($endWeek)){
                $startFormatted .= $startWeek->format(' Y');
            }

            $yearWeekArray[] = [
                'year' => $year,
                'week' => $week,
                'index' => $index,
                'formatted' => $startFormatted.' - '.$endFormatted
            ];

            $startDate->addWeek();
        }

        return $yearWeekArray;
    }
}
