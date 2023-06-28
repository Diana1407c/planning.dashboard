<?php

namespace App\Http\Requests;

use App\Services\DateService;
use Illuminate\Foundation\Http\FormRequest;

class TeamworkRequest extends FormRequest
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
        return [];
    }

    public function filters(): array
    {
        return [
            'type' => $this->get('type'),
            'stack_ids' => $this->get('stack_ids'),
            'technology_ids' => $this->get('technology_ids'),
            'engineer_ids' => $this->get('engineer_ids'),
            'range' => DateService::rangeToMonths($this->get('start_date'), $this->get('end_date')),
        ];
    }
}
