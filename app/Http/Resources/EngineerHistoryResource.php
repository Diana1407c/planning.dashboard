<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EngineerHistoryResource extends JsonResource
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
            'historyable_type' => $this->historyable_type,
            'historyable_id' => $this->historyable_id,
            'value' => $this->value,
            'label' => $this->label,
            'created_at' => $this->created_at,
        ];
    }
}
