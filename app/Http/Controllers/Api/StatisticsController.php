<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PlannedHour;
use App\Models\Project;
use App\Services\EngineerService;
use App\Services\HolidayService;
use App\Services\PlannedHourService;
use App\Services\Teamwork\TeamworkService;
use App\Support\Interval\GenericInterval;
use App\Support\Interval\Period;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function history(TeamworkService $teamworkService, PlannedHourService $plannedHourService, Request $request): JsonResponse
    {
        $filters = $request->except(['period_type', 'start_date', 'end_date']);

        $interval = GenericInterval::fromString(
            $request->get('period_type'),
            $request->get('start_date'),
            $request->get('end_date')
        );
        $plannedHours = $plannedHourService->plannedHoursCollection($filters, $interval);
        $twHoursData = $teamworkService->periodProjectHours($filters, $interval);

        $data = [["Period", "PM", "TL", "TW"]];
        $periods = $interval->Periods();

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

    public function pieChartReport(PlannedHourService $plannedHourService, Request $request): JsonResponse
    {
        $filters = $request->except(['period_type', 'start_date', 'end_date']);

        $interval = GenericInterval::fromString(
            $request->get('period_type'),
            $request->get('start_date'),
            $request->get('end_date')
        );

        $projectTypes = Project::indexedTypes();
        $hoursPerProjectType = $plannedHourService->hoursPerProjectType($filters, $interval);
        $data = [["Project Type", "Hours"],];

        foreach ($projectTypes as $type) {
            $hours = $hoursPerProjectType[$type['id']] ?? 0;
            $data[] = [$type['name'], (int)$hours];
        }

        return response()->json($data);
    }

    public function capacityReport(PlannedHourService $plannedHourService, HolidayService $holidayService, EngineerService $engineerService, Request $request): JsonResponse
    {
        $filters = $request->except(['period_type', 'start_date', 'end_date']);

        $interval = GenericInterval::fromString(
            $request->get('period_type'),
            $request->get('start_date'),
            $request->get('end_date')
        );

        $hoursPerProject = $plannedHourService->capacityHoursPerProject($filters, $interval);
        $data = [["Project", "Capacity Hours"]];

        $engineerCount = $engineerService->countEngineersByFilter($filters);
        $totalWorkingHours = $holidayService->workHoursPerInterval($interval);
        $companyCapacityHours = $engineerCount * $totalWorkingHours;

        $sumPlannedHours = array_sum($hoursPerProject->toArray());
        $unplannedHours = $companyCapacityHours - $sumPlannedHours;

        $data[] = ['Unplanned projects', (int)$unplannedHours];

        foreach ($hoursPerProject as $projectName => $capacityHours) {
            $data[] = [$projectName, (int)$capacityHours];
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
