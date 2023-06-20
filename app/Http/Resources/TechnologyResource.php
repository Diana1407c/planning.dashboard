<?php

namespace App\Http\Resources;

use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TechnologyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Technology $this */
        return [
            'id' => $this->id,
            'name' => $this->name,
            'stack_id' => $this->stack_id
        ];
    }
}
