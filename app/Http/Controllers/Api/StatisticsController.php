<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlannedHour;
use App\Services\PlannedHourService;
use App\Services\Teamwork\TeamworkService;
use App\Support\GenericPeriod;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
class StatisticsController extends Controller
{
    public function history(TeamworkService $teamworkService,PlannedHourService $plannedHourService, Request $request): JsonResponse
    {
        $periodType=$request->get('period_type');
        $projectIds=$request->get('project_ids');

        if($periodType==PlannedHour::WEEK_PERIOD_TYPE){
            $startDate=Carbon::now()->subMonths(3)->startOfWeek();
            $endDate=Carbon::now()->endOfWeek();
            $addFunction = 'addWeek';
        }
        else{
            $startDate=Carbon::now()->subMonths(3)->startOfMonth();
            $endDate=Carbon::now()->endOfMonth();
            $addFunction = 'addMonth';
        }

        $plannedHours = $plannedHourService->plannedHoursCollection($projectIds,$periodType, $startDate, $endDate);
        $data = [["Period", "PM", "TL", "TW"]];

        for ($date = $startDate->copy(); $date->lte($endDate); $date->$addFunction()) {
            $periodLabel = $this->getPeriodLabel($date, $periodType);
            $pmHours = $this->getHoursForType($plannedHours, PlannedHour::TECHNOLOGY_TYPE, $date, $periodType);
            $tlHours = $this->getHoursForType($plannedHours, PlannedHour::ENGINEER_TYPE, $date, $periodType);

            $rowData = [
                $periodLabel,
                (int) $pmHours,
                (int) $tlHours,
                100,
            ];
            $data[] = $rowData;
        }

        return response()->json($data);
    }

    protected function getPeriodLabel(Carbon $date, string $periodType): string
    {
        if ($periodType == PlannedHour::WEEK_PERIOD_TYPE) {
            return "{$date->format('d.m.Y')} - {$date->clone()->endOfWeek()->format('d.m.Y')}";
        }
        return "{$date->format('d.m.Y')} - {$date->clone()->endOfMonth()->format('d.m.Y')}";
    }

    protected function getHoursForType($plannedHours, string $planableType, Carbon $date, string $periodType)
    {
        return $plannedHours
            ->where('planable_type', $planableType)
            ->where('year', $date->year)
            ->where('period_number', $this->getPeriodNumber($date, $periodType))
            ->pluck('sum_hours')
            ->first() ?? 0;
    }

    protected function getPeriodNumber(Carbon $date, string $periodType)
    {
        return $periodType == PlannedHour::WEEK_PERIOD_TYPE
            ? $date->weekOfYear
            : $date->month;
    }
}
