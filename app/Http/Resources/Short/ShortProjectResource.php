<?php

namespace App\Http\Resources\Short;

use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Project $this */
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
