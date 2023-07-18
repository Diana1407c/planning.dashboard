<?php

namespace App\Http\Requests\Planning;

use App\Models\PlannedHour;

class WeeklyPMPlanningFilterRequest extends BasePlanningFilterRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function filter(): array
    {
        return parent::filter() + [
                'period_type' => PlannedHour::WEEK_PERIOD_TYPE,
                'planable_type' => PlannedHour::TECHNOLOGY_TYPE,
            ];
    }
}
