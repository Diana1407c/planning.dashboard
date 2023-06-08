<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class EngineerTLPlanningResource extends EngineerResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $collection = $this->getTLPlanning($request);
        $total_time_planned = $collection->sum('time');

        return parent::toArray($request) + [
            'tl_planning' => $collection,
            'total' => $total_time_planned
        ];
    }

    protected function getTLPlanning(Request $request)
    {
        $query = $this->teamLeadPlannings();

        if($year = $request->get('year')){
            $query->where('year', $year);
        }

        if($year = $request->get('week')){
            $query->where('week', $year);
        }

        return TLPlanningResource::collection($query->get());
    }
}
