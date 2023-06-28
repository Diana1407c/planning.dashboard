<?php

namespace App\Http\Resources;

use App\Models\Engineer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StackTechnologiesEngineersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Engineer $this */
        return [
            'id' => $this->id,
            'name' => $this->fullName(),
            'team_id' => $this->team_id,
            'technology_id' => $this->team->technology_id,
            'stack_id' => $this->team->technology->stack_id,
        ];
    }
}
