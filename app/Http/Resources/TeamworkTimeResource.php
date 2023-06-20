<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamworkTimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'engineer_id' => $this->engineer_id,
            'engineer_name' => $this->last_name.' '.$this->first_name,
            'stack_id' => $this->stack_id,
            'technology_id' => $this->technology_id,
            'date_index' => $this->year.'_'.$this->month,
            'hours' => number_format($this->hours, 2)
        ];
    }
}
