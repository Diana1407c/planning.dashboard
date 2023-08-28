<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlannedHour;
use App\Services\PlannedHourService;
use App\Services\Teamwork\TeamworkService;
use App\Support\Interval\GenericInterval;
use App\Support\Interval\Period;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function history(TeamworkService $teamworkService, PlannedHourService $plannedHourService, Request $request): JsonResponse
    {
        $projectTypes = $request->get('project_types');
        $projectIds = $request->get('project_ids');

        $interval = GenericInterval::fromString(
            $request->get('period_type'),
            $request->get('start_date'),
            $request->get('end_date')
        );
        $plannedHours = $plannedHourService->plannedHoursCollection($projectTypes, $projectIds,$interval);
        $twHoursData = $teamworkService->periodProjectHours($projectTypes, $projectIds,$interval);

        $data = [["Period", "PM", "TL", "TW"]];
        $periods = $interval->getPeriods();

        foreach ($periods as $period) {
            $periodLabel = $period->toLabel();
            $pmHours = $this->getHoursForType($plannedHours, PlannedHour::TECHNOLOGY_TYPE, $period);
            $tlHours = $this->getHoursForType($plannedHours, PlannedHour::ENGINEER_TYPE, $period);
            $twHours = $this->getTeamworkHours($twHoursData, $period);

            $rowData = [
                $periodLabel,
                (int)$pmHours,
                (int)$tlHours,
                $twHours,
            ];
            $data[] = $rowData;
        }

        return response()->json($data);
    }

    protected function getTeamworkHours($twHoursData, Period $period)
    {
        return $twHoursData
            ->where('year', $period->date->year)
            ->where('period_number', $period->periodNumber())
            ->pluck('tw_sum_hours')
            ->first() ?? 0;
    }

    protected function getHoursForType($plannedHours, string $planableType, Period $period)
    {
        return $plannedHours
            ->where('planable_type', $planableType)
            ->where('year', $period->date->year)
            ->where('period_number', $period->periodNumber())
            ->pluck('sum_hours')
            ->first() ?? 0;
    }
}
