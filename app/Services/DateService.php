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
