<?php

namespace App\Http\Resources;

use App\Models\TLPlanning;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TLPlanningResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var TLPlanning $this */
        return [
            'id' => $this->id,
            'project_name' => $this->project->name,
            'project_id' => $this->project_id,
            'engineer_id' =>  $this->engineer_id,
            'year' => $this->year,
            'week' => $this->week,
            'time' => $this->time
        ];
    }
}
