<?php

namespace App\Matrix;

use App\Models\PlannedHour;
use App\Services\PlannedHourService;
use App\Services\ProjectService;
use App\Services\Teamwork\TeamworkService;
use Carbon\Carbon;

class ComparisonMatrix
{
    protected Carbon|null $fromDate;
    protected Carbon|null $toDate;
    protected $plannedHourService;

    protected $tlPlannedHours;
    protected $pmPlannedHours;
    protected $projects;
    protected $loggedTimes;

    public function __construct(protected array $filter, protected string $periodType)
    {
        $this->initProjects();
        $this->setDates();
        $this->setPlannedHours();
        $this->setLoggedTimes();
    }

    public function setPlannedHours(): void
    {
        $this->tlPlannedHours = $this->plannedHourService()->groupedHoursByFilter([
            'from_year' => $this->fromDate->year,
            'from_period_number' => $this->isWeekly() ? $this->fromDate->weekOfYear : $this->fromDate->month,
            'to_year' => $this->toDate->year,
            'to_period_number' => $this->isWeekly() ? $this->toDate->weekOfYear : $this->toDate->month,
            'period_type' => $this->periodType,
            'projects_ids' => $this->projects->pluck('id'),
            'planable_type' => PlannedHour::ENGINEER_TYPE,
        ]);

        $this->pmPlannedHours = $this->plannedHourService()->groupedHoursByFilter([
            'from_year' => $this->fromDate->year,
            'from_period_number' => $this->isWeekly() ? $this->fromDate->weekOfYear : $this->fromDate->month,
            'to_year' => $this->toDate->year,
            'to_period_number' => $this->isWeekly() ? $this->toDate->weekOfYear : $this->toDate->month,
            'period_type' => $this->periodType,
            'projects_ids' => $this->projects->pluck('id'),
            'planable_type' => PlannedHour::TECHNOLOGY_TYPE,
        ]);
    }

    public function setLoggedTimes(): void
    {
        $this->loggedTimes = TeamworkService::groupedHours([
            'projects_ids' => $this->projects->pluck('id'),
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
        ], $this->periodType);
    }

    public function initProjects(): void
    {
        $this->projects = ProjectService::filter($this->filter);
    }

    public function matrix(): array
    {
        if ($this->isWeekly()) {
            return $this->weeklyMatrix();
        }

        return $this->monthlyMatrix();
    }

    public function weeklyMatrix(): array
    {
        $dates = [];
        $hours = [];

        for ($i = $this->fromDate->clone(); $i->lte($this->toDate); $i->addWeek()) {
            $this->hoursByPeriod($hours, $i->year, $i->weekOfYear);
            $dates[$i->year . '_' . $i->weekOfYear] = $i->format('j M') . ' - ' . $i->clone()->endOfWeek()->format('j M Y');
        }

        return [
            'dates' => $dates,
            'projects' => $this->projects,
            'report' => $hours,
        ];
    }

    public function monthlyMatrix(): array
    {
        $dates = [];
        $hours = [];

        for ($i = $this->fromDate->clone(); $i->lte($this->toDate); $i->addMonth()) {
            $this->hoursByPeriod($hours, $i->year, $i->month);
            $dates[$i->year . '_' . $i->month] = $i->format('M Y');
        }

        return [
            'dates' => $dates,
            'projects' => $this->projects,
            'report' => $hours,
        ];
    }

    protected function hoursByPeriod(&$data, int $year, int $periodNumber): void
    {
        foreach ($this->projects as $project) {
            $data[$project->id][$year . '_' . $periodNumber]['PM']
                = $this->pmPlannedHours->where('project_id', $project->id)
                ->where('year', $year)
                ->where('period_number', $periodNumber)
                ->first()->sum_hours ?? 0;

            $data[$project->id][$year . '_' . $periodNumber]['TL']
                = $this->tlPlannedHours->where('project_id', $project->id)
                ->where('year', $year)
                ->where('period_number', $periodNumber)
                ->first()->sum_performance_hours ?? 0;

            $temWorkTime = $this->loggedTimes->where('project_id', $project->id)->where('project_id', $project->id)
                ->where('year', $year)
                ->where('period_number', $periodNumber)
                ->first()->sum_hours ?? 0;

            $data[$project->id][$year . '_' . $periodNumber]['TM'] = round($temWorkTime, 2);
        }
    }

    protected function setDates(): void
    {
        $this->fromDate = empty($this->filter['start_date']) ? Carbon::now() : Carbon::parse($this->filter['start_date']);
        $this->toDate = empty($this->filter['end_date']) ? $this->fromDate->clone()->addMonth() : Carbon::parse($this->filter['end_date']);

        if ($this->isWeekly()) {
            $this->fromDate->startOfWeek()->startOfDay();
            $this->toDate->endOfWeek()->endOfDay();

            return;
        }

        $this->fromDate->startOfMonth()->startOfDay();
        $this->toDate->endOfMonth()->endOfDay();
    }

    protected function isWeekly(): bool
    {
        return $this->periodType == PlannedHour::WEEK_PERIOD_TYPE;
    }

    protected function plannedHourService(): PlannedHourService
    {
        if (!$this->plannedHourService) {
            $this->plannedHourService = new PlannedHourService();
        }

        return $this->plannedHourService;
    }
}
