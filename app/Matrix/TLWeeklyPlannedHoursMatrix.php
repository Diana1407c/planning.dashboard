<?php

namespace App\Matrix;

use App\Models\PlannedHour;
use App\Services\Teamwork\TeamworkService;

class TLWeeklyPlannedHoursMatrix extends TLBaseMatrix
{

    public function matrix(): array
    {
        $this->setHours();
        $this->setTechnologyData();
        $this->setEngineersData();
        $this->setPrevWeekData();
        $this->setMonthlyData();

        return $this->data;
    }

    protected function setPrevWeekData()
    {
        $filterPrevWeek = $this->filter->clone();
        $filterPrevWeek->period->from->subWeek();
        $filterPrevWeek->period->to->subWeek();
        $filterPrevWeek->set('year', $filterPrevWeek->period->from->year)
            ->set('period_number', $filterPrevWeek->period->from->weekOfYear);
        $tlHoursPrevWeek = $this->plannedHourService()->hoursByFilter($filterPrevWeek->params)
            ->groupBy(['planable_id', 'project_id']);

        $twHours = TeamworkService::engineersHours([
            'from_date' => $filterPrevWeek->period->from,
            'to_date' => $filterPrevWeek->period->to,
        ])->groupBy(['engineer_id', 'project_id']);


        foreach ($tlHoursPrevWeek as $engineerId => $engineerHours) {
            foreach ($engineerHours as $projectId => $hour) {
                $this->data['prev_planned'][$engineerId][$projectId] = $hour->first()->hours;
            }
        }

        foreach ($twHours as $engineerId => $engineerHours) {
            foreach ($engineerHours as $projectId => $hour) {
                $this->data['prev_worked'][$engineerId][$projectId] = twHours($hour->sum('sum_hours'));
            }
        }
    }

    protected function setMonthlyData()
    {
        $pmFilter = $this->filter->clone()->set('planable_type', PlannedHour::TECHNOLOGY_TYPE);
        $pmFilter->period->from->startOfMonth();
        $pmFilter->period->to = $pmFilter->period->from->clone()->endOfMonth();
        $pmFilter->set('year', $pmFilter->period->from->year)
            ->set('period_number', $pmFilter->period->from->month)
            ->set('period_type', PlannedHour::MONTH_PERIOD_TYPE);

        $pmMonthHours = $this->plannedHourService()->hoursByFilter($pmFilter->params)
            ->groupBy(['project_id', 'planable_id']);

        $this->data['month_planned'] = [];
        foreach ($pmMonthHours as $projectId => $projectHours) {
            foreach ($projectHours as $technologyId => $hour) {
                $this->data['month_planned'][$projectId][$technologyId] = $hour->first()->hours;
            }
        }

        $twMonthHours = TeamworkService::engineersHours([
            'from_date' => $pmFilter->period->from,
            'to_date' => $pmFilter->period->to,
        ])->groupBy(['project_id', 'engineer_id']);

        foreach ($twMonthHours as $projectId => $projectHours) {
            foreach ($projectHours as $engineerId => $hour) {
                $technologyId = $this->detectTechnologyIdByData($this->data['month_planned'], $projectId, $engineerId);
                $this->data['month_worked'][$projectId][$technologyId] = twHours($hour->sum('sum_hours'));
            }
        }
    }

    protected function detectTechnologyIdByData($data, $projectId, $engineerId)
    {
        if (!isset($this->technologyEngineers[$engineerId])) {
            return null;
        }

        $technologies = $this->technologyEngineers[$engineerId];

        if ($technologies->count() > 1) {
            foreach ($technologies as $technology) {
                if (isset($data[$projectId][$technology->technology_id])) {
                    return $technology->technology_id;
                }
            }
        }

        return $technologies->first()->technology_id;
    }
}
