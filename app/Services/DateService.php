<?php

namespace App\Services;

use Illuminate\Support\Carbon;

class DateService
{
    public static function convertToWeek($date)
    {
        $carbonDate = Carbon::createFromFormat('Y-m-d', $date);

        return !$carbonDate ? [] : [
            'week' => $carbonDate->weekOfYear,
            'year' => $carbonDate->year,
        ];
    }

    public static function convertDatesToWeek($startDate, $endDate)
    {
        $carbonEndDate = Carbon::createFromFormat('Y-m-d', $endDate);

        return !$carbonEndDate ? [] : self::convertToWeek($startDate) + [
            'end_week' => $carbonEndDate->weekOfYear,
            'end_year' => $carbonEndDate->year,
        ];
    }
}
