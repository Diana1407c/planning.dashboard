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
        $technology = $this->team->technologies()->first();

        /** @var Engineer $this */
        return [
            'id' => $this->id,
            'name' => $this->fullName(),
            'team_id' => $this->team_id,
            'technology_id' => $technology ? $technology->id : null,
            'stack_id' => $technology ? $technology->stack_id : null,
        ];
    }
}
