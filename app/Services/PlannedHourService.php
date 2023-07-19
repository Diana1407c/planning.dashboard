<?php

namespace App\Services;

use App\Models\PlannedHour;

class PlannedHourService
{
    public function hoursByFilter(array $filter)
    {
        $query = PlannedHour::query();

        if (!empty($filter['year'])) {
            $query->where('year', $filter['year']);
        }

        if (!empty($filter['period_type'])) {
            $query->where('period_type', $filter['period_type']);
        }

        if (!empty($filter['period_number'])) {
            $query->where('period_number', $filter['period_number']);
        }

        if (!empty($filter['planable_type'])) {
            $query->where('planable_type', $filter['planable_type']);
        }

        if (!empty($filter['planable_ids'])) {
            $query->whereIn('planable_id', $filter['planable_ids']);
        }

        return $query->get();
    }

    public function storeHours(array $attributes, array $values): void
    {
        if ($values['hours'] == 0) {
            PlannedHour::where($attributes)->delete();

            return;
        }

        PlannedHour::query()->updateOrCreate($attributes, $values);
    }
}
