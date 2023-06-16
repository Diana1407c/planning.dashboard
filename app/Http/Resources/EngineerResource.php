<?php

namespace App\Http\Resources;

use App\Models\Engineer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EngineerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->fullName(),
            'email' => $this->email,
            'username' => $this->username,
            'team_name' => $this->team->name,
            'team_id' => $this->team_id,
            'plannings' => $this->plannings()
        ];
    }

    protected function plannings(): array
    {
        $plannings['total'] = $this->teamLeadPlannings->sum('hours');

        $plannings['details'] = $this->teamLeadPlannings->groupBy('project_id')
            ->map(function ($groupedItems) {
                return [
                    'project' => $groupedItems->first()->project->name,
                    'hours' => $groupedItems->sum('hours'),
                ];
            })->values();

        return $plannings;
    }
}
