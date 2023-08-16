<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlannedHour;
use App\Models\Project;
use App\Models\Team;
use App\Models\Technology;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class StatisticsController extends Controller
{
    public function history(Project $project, Request $request): JsonResponse
    {
        $periodType = $request->get('period_type');
        $plannedHours = PlannedHour::all();

        $teams = Team::query()->select('teams.*')
            ->join('engineers', function ($join) use ($plannedHours) {
                $join->on('teams.id', 'engineers.team_id')
                    ->whereIn('engineers.id', $plannedHours->where('planable_type', PlannedHour::ENGINEER_TYPE)->pluck('planable_id'));
            })->with('members')->distinct('teams.id')->get();;

        $technologies = Technology::query()->whereIn('id', $plannedHours->where('planable_type', PlannedHour::TECHNOLOGY_TYPE)->pluck('planable_id'))->get();

        $data = [["Week", "PM", "TL", "TW"]];
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

        foreach ($plannedHours->where('period_type', $periodType)->sortBy('period_number') as $hour) {
            $startDate = Carbon::now()->setISODate($hour->year, $hour->period_number)->startOfWeek();
            $endDate = Carbon::now()->setISODate($hour->year, $hour->period_number)->endOfWeek();
            $weekData = "{$hour->period_number} - {$startDate->format('d.m')} - {$endDate->format('d.m')}";

            $totalTlHours = 0;
            foreach ($teams as $team) {
                foreach ($team->members as $member) {
                    $totalTlHours += $hours['tl'][$member->id];
                }
            }

            $rowData = [
                "$weekData",
                $hours['pm'][$technology->id],
                $totalTlHours,
                100,
            ];

            $data[] = $rowData;
        }

        return response()->json($data);
    }
}
