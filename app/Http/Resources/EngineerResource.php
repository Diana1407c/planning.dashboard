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
            'team_name' => $this->team->name,
            'team_id' => $this->team_id
        ];
    }
}
