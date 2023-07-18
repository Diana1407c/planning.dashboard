<?php

namespace App\Http\Requests;

use App\Models\PlannedHour;
use Illuminate\Foundation\Http\FormRequest;

class TLPlanningRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'project_id' => 'required|integer',
            'engineer_id' => 'required|integer',
            'year' => 'required|integer',
            'period_type' => 'required|in:' . implode(',', [
                    PlannedHour::WEEK_PERIOD_TYPE,
                    PlannedHour::MONTH_PERIOD_TYPE,
                ]),
            'period_number' => 'required|integer',
            'hours' => 'required|integer'
        ];
    }
}
