<?php

namespace App\Http\Requests\Planning;

use Illuminate\Foundation\Http\FormRequest;

class BasePlanningFilterRequest extends FormRequest implements PlanningFilterInterface
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
            'team_ids' => '',
            'project_ids' => '',
            'year' => '',
            'period_number' => '',
        ];
    }

    public function filter(): array
    {
        return $this->validated();
    }
}
