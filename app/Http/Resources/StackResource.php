<?php

namespace App\Http\Resources;

use App\Models\Stack;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Stack $this */
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }
}
