<?php

namespace App\Matrix;

use App\Models\Engineer;
use App\Models\EngineerPerformance;
use App\Models\PlannedHour;
use App\Services\PlannedHourService;
use App\Support\Filters\PlannedHoursFilter;

class TLBaseMatrix
{
    protected $hours;
    protected $data;
    protected $technologyEngineers;
    protected $plannedHourService;

    public function __construct(protected PlannedHoursFilter $filter)
    {
    }

    protected function setHours()
    {
        $params = $this->filter->params;
        $params['planable_type'] = null;

        $this->hours = $this->plannedHourService()->hoursByFilter($params);
    }

    protected function setTechnologyEngineers()
    {
        $this->technologyEngineers = Engineer::query()
            ->select(['engineers.id', 'team_technology.technology_id'])
            ->join('teams', 'teams.id', '=', 'engineers.team_id')
            ->join('team_technology', 'team_technology.team_id', '=', 'teams.id')
            ->get()->groupBy('id');
    }

    protected function setTechnologyData()
    {
        $pmHours = $this->hours->where('planable_type', PlannedHour::TECHNOLOGY_TYPE)
            ->groupBy(['planable_id', 'project_id']);

        $this->data['technologies']['planned_pm'] = [];
        foreach ($pmHours as $technologyId => $technologyHours) {
            $this->data['technologies']['planned_pm']['total'][$technologyId] = 0;
            foreach ($technologyHours as $projectId => $hour) {
                $sumHours = $hour->first()->hours;
                $this->data['technologies']['planned_pm'][$technologyId][$projectId] = $sumHours;
                $this->data['technologies']['planned_pm']['total'][$technologyId] += $sumHours;
            }
        }
    }

    protected function setEngineersData()
    {
        $this->setTechnologyEngineers();

        $tlHours = $this->hours->where('planable_type', PlannedHour::ENGINEER_TYPE)
            ->groupBy(['planable_id', 'project_id']);

        foreach ($tlHours as $engineerId => $engineerHours) {
            $this->data['engineers'][$engineerId]['total'] = 0;
            foreach ($engineerHours as $projectId => $hour) {
                $engineerHours = $hour->first()->hours;
                $this->data['engineers'][$engineerId][$projectId] = $engineerHours;
                $this->data['engineers'][$engineerId]['total'] += $engineerHours;

                $technologyId = $this->detectTechnologyId($projectId, $engineerId);

                if (!isset($this->data['technologies']['planned_tl'][$technologyId][$projectId])) {
                    $this->data['technologies']['planned_tl'][$technologyId][$projectId] = 0;
                }
                $this->data['technologies']['planned_tl'][$technologyId][$projectId] += $hour->first()->performance_hours;
            }
        }

        if (isset($this->data['technologies']['planned_tl'])) {
            foreach ($this->data['technologies']['planned_tl'] as $technologyId => $projectHours) {
                $this->data['technologies']['planned_tl']['total'][$technologyId] = array_sum($projectHours);
            }
        }
    }

    protected function detectTechnologyId($projectId, $engineerId)
    {
        if (!isset($this->technologyEngineers[$engineerId])) {
            return null;
        }

        $technologies = $this->technologyEngineers[$engineerId];

        if ($technologies->count() > 1) {
            foreach ($technologies as $technology) {
                if (isset($this->data['technologies']['planned_pm'][$technology->technology_id][$projectId])) {
                    return $technology->technology_id;
                }
            }
        }

        return $technologies->first()->technology_id;
    }

    protected function plannedHourService(): PlannedHourService
    {
        if (!$this->plannedHourService) {
            $this->plannedHourService = new PlannedHourService();
        }

        return $this->plannedHourService;
    }

    protected function setProjectData()
    {
        $projectPerformance = EngineerPerformance::query()
            ->whereNotNull('performance')
            ->where('is_current', true)
            ->groupBy(['engineer_id', 'project_id'])
            ->selectRaw('performance, engineer_id, project_id')
            ->get();

        foreach ($projectPerformance as $performance) {
            $engineerId = $performance->engineer_id;
            $projectId = $performance->project_id;
            $performance = $performance->performance;

            if (!isset($this->data['project'][$projectId])) {
                $this->data['project'][$projectId] = [];
            }

            $this->data['project'][$projectId][$engineerId] = $performance;
        }
    }
}
