<?php

namespace App\Services;

use App\Models\PlannedHour;
use Carbon\Carbon;

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

    public function groupedHoursByFilter(array $filter)
    {
        $query = PlannedHour::query()->select([
            'project_id',
            'planable_type',
            'year',
            'period_number',
        ])->selectRaw('SUM(hours) as sum_hours');

        if (!empty($filter['from_year'])) {
            $query->where('year', '>=', $filter['from_year']);
        }

        if (!empty($filter['from_year']) && !empty($filter['from_period_number'])) {
            $query->where(function ($q) use ($filter) {
                $q->where(function ($qYear) use ($filter) {
                    $qYear->where('year', '=', $filter['from_year'])
                        ->where('period_number', '>=', $filter['from_period_number']);
                })->orWhere('year', '>', $filter['from_year']);
            });
        }

        if (!empty($filter['to_year']) && !empty($filter['to_period_number'])) {
            $query->where(function ($q) use ($filter) {
                $q->where(function ($qYear) use ($filter) {
                    $qYear->where('year', '=', $filter['to_year'])
                        ->where('period_number', '<=', $filter['to_period_number']);
                })->orWhere('year', '<', $filter['to_year']);
            });
        }

        if (!empty($filter['period_type'])) {
            $query->where('period_type', $filter['period_type']);
        }

        if (!empty($filter['projects_ids'])) {
            $query->whereIn('project_id', $filter['projects_ids']);
        }

        $query->groupBy([
            'project_id',
            'planable_type',
            'year',
            'period_number',
        ]);

        return $query->get();
    }
}
