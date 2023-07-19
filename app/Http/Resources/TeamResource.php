<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Team $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'technology' => TechnologyResource::make($this->technology)->toArray($request),
            'team_lead' => [
                'id' => $this->teamLead->id,
                'email' => $this->teamLead->email,
                'name' => $this->teamLead->fullName()
            ],
            'members' => EngineerResource::collection($this->members)
        ];
    }
}
