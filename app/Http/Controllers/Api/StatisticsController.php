<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Http\Resources\TechnologyResource;
use App\Models\PlannedHour;
use App\Models\Project;
use App\Models\Team;
use App\Models\Technology;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class StatisticsController extends Controller
{
//    public function history(Request $request): JsonResponse
//    {
//        $data = [
//            ["Week", "PM", "TL", "TW"],
//            ['31.07-07.08',  120, 120, 112],
//            ['07.08-14.08',  120, 120, 120],
//            ['14.08-21.08',  120, 120, 120],
//            ['21.08-28.08',  120, 120, 104],
//            ['28.08-04.09',  120, 120, 128],
//        ];
//
//        return response()->json($data);
//
//    }

    public function history(Project $project, Request $request): JsonResponse
    {
        $plannedHours = PlannedHour::all();

        $teams = Team::query()->select('teams.*')
            ->join('engineers', function ($join) use ($plannedHours) {
                $join->on('teams.id', 'engineers.team_id')
                    ->whereIn('engineers.id', $plannedHours->where('planable_type', PlannedHour::ENGINEER_TYPE)->pluck('planable_id'));
            })->with('members')->distinct('teams.id')->get();;

        $technologies = Technology::query()->whereIn('id', $plannedHours->where('planable_type', PlannedHour::TECHNOLOGY_TYPE)->pluck('planable_id'))->get();

        $data = [
            ["Week", "PM", "TL", "TW"],
        ];

        $hours = [];

        foreach ($technologies as $technology) {
            $hours['pm'][$technology->id] = $plannedHours->where('planable_type', PlannedHour::TECHNOLOGY_TYPE)
                ->where('planable_id', $technology->id)->first()->hours ?? 0;
        }

        foreach ($teams as $team) {
            foreach ($team->members as $member) {
                $hours['tl'][$member->id] = $plannedHours->where('planable_type', PlannedHour::ENGINEER_TYPE)
                    ->where('planable_id', $member->id)->first()->performance_hours ?? 0;
            }
        }

        foreach ($plannedHours->where('period_type', 'week') as $hour) {
            $rowData = [
                $hour->period_number,
                $hours['pm'][$technology->id],
                $hours['tl'][$member->id],
                88,
            ];

            $data[] = $rowData;
        }

        return response()->json($data);
    }
}
