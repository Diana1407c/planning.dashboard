<?php

namespace App\Services;

use App\Http\Requests\TeamworkRequest;
use App\Http\Resources\TeamworkTimeResource;
use App\Services\Teamwork\TeamworkService;
use Illuminate\Http\Request;

class ReportService
{
    public static function comparisonData(Request $request): array
    {
        $projects = ProjectService::filter($request);
        $dates = DateService::rangeToWeeks($request->get('start_date'), $request->get('end_date'));
        $datesArray = DateService::weeksDateArray($dates);

        $PMPlannings = PMPlanningService::sumHoursByProjectAndDate(['range' => $dates]);
        $TLPlannings = TLPlanningService::sumHoursByProjectAndDate(['range' => $dates]);

        $rawDates = [];
        $report = [];
        foreach ($datesArray as $date){
            $rawDates[$date['index']] = $date['formatted'];

            foreach ($projects as $project) {
                $report[$project->id][$date['index']]['PM']
                    = intval($PMPlannings->where('project_id', $project->id)
                    ->where('year', $date['year'])
                    ->where('week', $date['week'])
                    ->value('sum_hours')) ?? 0;

                $report[$project->id][$date['index']]['TL']
                    = intval($TLPlannings->where('project_id', $project->id)
                    ->where('year', $date['year'])
                    ->where('week', $date['week'])
                    ->value('sum_hours')) ?? 0;
            }
        }

        return [
            'dates' => $rawDates,
            'projects' => $projects,
            'report' => $report
        ];
    }

    public static function teamworkData(TeamworkRequest $request): array
    {
        $filters = $request->filters();
        $datesArray = DateService::monthsDateArray($filters['range']);
        $loggedTime = collect(TeamworkTimeResource::collection(TeamworkService::getLoggedTime($filters))->toArray($request));

        $groupedLogged = $loggedTime->groupBy(['date_index', 'stack_id', 'technology_id', 'engineer_id']);
        $headingColumns = $loggedTime->groupBy(['stack_id', 'technology_id', 'engineer_id']);

        foreach ($headingColumns as $stackIndex => $stackGroup){
            foreach ($stackGroup as $techIndex => $techGroup){
                foreach ($techGroup as $engineerIndex => $engineer){
                    $headingColumns[$stackIndex][$techIndex][$engineerIndex] = $engineer->first()['engineer_name'];
                }
            }
        }

        $grandTotals = [];
        foreach ($groupedLogged as $dateIndex => $dateGroup){
            foreach ($dateGroup as $stackIndex => $stackGroup){
                foreach ($stackGroup as $techIndex => $techGroup){
                    foreach ($techGroup as $engineerIndex => $engineer){
                        $groupedLogged[$dateIndex][$stackIndex][$techIndex][$engineerIndex] = $engineer->first()['hours'];
                        $grandTotals['engineers'][$engineerIndex] = isset($grandTotals['engineers'][$engineerIndex]) ? $grandTotals['engineers'][$engineerIndex] + round($engineer->first()['hours'], 2) : round($engineer->first()['hours'], 2);
                    }

                    $grandTotals['tech'][$techIndex] = isset($grandTotals['tech'][$techIndex]) ? $grandTotals['tech'][$techIndex] + round($techGroup->sum(), 2) : round($techGroup->sum(), 2);
                    $groupedLogged[$dateIndex][$stackIndex][$techIndex]['total_tech'] = round($techGroup->sum(), 2);
                }
                $grandTotals['stack'][$stackIndex] = isset($grandTotals['stack'][$stackIndex]) ? $grandTotals['stack'][$stackIndex] + round($stackGroup->sum('total_tech'), 2) : round($stackGroup->sum('total_tech'), 2);
                $groupedLogged[$dateIndex][$stackIndex]['total_stack'] = round($stackGroup->sum('total_tech'), 2);
            }
            $groupedLogged[$dateIndex]['total_month'] = round($dateGroup->sum('total_stack'), 2);
        }

        $grandTotals['total'] = round($groupedLogged->sum('total_month'), 2);

        return [
            'dates' => $datesArray,
            'headingGroup' => $headingColumns,
            'logging' => $groupedLogged,
            'grandTotals' => $grandTotals
        ];
    }
}
