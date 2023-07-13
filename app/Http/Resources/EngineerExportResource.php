<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EngineerExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->fullName(),
            'email' => $this->email,
            'team_name' => $this->team->name,
            'hours' => $this->teamLeadPlannings->sum('hours')
        ];
    }
}
