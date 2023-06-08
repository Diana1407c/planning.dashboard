<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class TeamTLPlanningResource extends TeamResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $members = $this->members();
        if($search = $request->get('search_engineer')){
            $members->where(function ($engineers) use ($search){
                $engineers->where('engineers.first_name', 'LIKE', "%$search%")
                    ->orWhere('engineers.last_name', 'LIKE', "%$search%");
            });
        }

        $collection = EngineerTLPlanningResource::collection($members->get());

        return parent::toArray($request) + [
                'members' => $collection,
                'total_team_planned' => $collection->sum(function ($item) use($request){
                    if($year = $request->get('year') && $week = $request->get('week')){
                        return $item->teamLeadPlannings
                            ->where('year', $year)
                            ->where('week', $week)->sum('time');
                    }

                    return $item->teamLeadPlannings->sum('time');
                })
        ];
    }
}
