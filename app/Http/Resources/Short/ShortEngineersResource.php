<?php

namespace App\Http\Resources\Short;

use App\Models\Engineer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShortEngineersResource extends JsonResource
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
            'name' => $this->fullName()
        ];
    }
}
