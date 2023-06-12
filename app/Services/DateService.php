<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class DateService
{
    public static function convertToWeek($date)
    {
        $carbonDate = Carbon::parse($date);

        return !$carbonDate ? [] : [
            'week' => $carbonDate->weekOfYear,
            'year' => $carbonDate->year,
        ];
    }

    public static function convertDatesToWeek($startDate, $endDate = null)
    {
        if($endDate){
            $carbonEndDate = Carbon::parse($endDate);
        } else {
            $carbonEndDate = Carbon::parse($startDate)->addMonth();
        }

        return self::convertToWeek($startDate) + [
                'end_week' => $carbonEndDate->weekOfYear,
                'end_year' => $carbonEndDate->year,
            ];
    }

    public static function generateYearWeekArray($dates)
    {
        $yearWeekArray = [];

        $startDate = Carbon::now()->setISODate($dates['year'], $dates['week'])->startOfWeek();
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
