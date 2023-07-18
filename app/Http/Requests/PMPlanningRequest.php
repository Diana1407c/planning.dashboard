<?php

namespace App\Http\Requests;

use App\Models\PlannedHour;
use Illuminate\Foundation\Http\FormRequest;

class PMPlanningRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'project_id' => 'required|integer',
            'technology_id' => 'required|integer',
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
