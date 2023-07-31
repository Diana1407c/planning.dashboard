<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EngineerPerformanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'performance' => 'nullable|int|min:0|max:100',
            'from_date' => 'required|date',
            'level_id' => 'required|int'
        ];
    }
}
