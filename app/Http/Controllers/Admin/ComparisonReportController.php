<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Short\ShortProjectResource;
use App\Models\Engineer;
use App\Models\PlannedHour;
use App\Models\Project;
use App\Models\Team;
use App\Services\PlannedHourService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

/**
 * Class ComparisionReportController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ComparisonReportController extends Controller
{
    public function index(Request $request)
    {
        $this->test();
        return Inertia::render('Reports/Comparison',[
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request)
        ])->withViewData([
            'title' => 'Comparison Report',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'ComparisonPlanning' => false,
            ],
        ]);
    }

    protected function test()
    {
        $this->plannedHourService = null;
        $engineers = Engineer::query()
            ->select(['engineers.id', 'team_technology.technology_id'])
            ->join('teams', 'teams.id', '=', 'engineers.team_id')
            ->join('team_technology', 'team_technology.team_id', '=', 'teams.id')
            ->get()->groupBy('id');

        $filter = [];
        $hours = $this->plannedHourService()->hoursByFilter($filter);
        $pmHours = $hours->where('planable_type', PlannedHour::TECHNOLOGY_TYPE)
            ->groupBy(['planable_id', 'project_id']);
        $data = [];
        foreach ($pmHours as $technologyId => $technologyHours) {
            foreach ($technologyHours as $projectId => $hour) {
                $data['technologies']['pm'][$technologyId][$projectId] = $hour->first()->hours;
            }
        }

        $tmHours = $hours->where('planable_type', PlannedHour::ENGINEER_TYPE)
            ->groupBy(['planable_id', 'project_id']);

        foreach ($tmHours as $engineerId => $engineerHours) {
            foreach ($engineerHours as $projectId => $hour) {
                $data['engineers'][$engineerId][$projectId] = $hour->first()->hours;

                if (!isset($engineers[$engineerId])) {
                    continue;
                }

                $technologyId = $this->detectTechnologyIds($data['technologies']['pm'], $projectId, $engineers[$engineerId]);
                if (!isset($data['technologies']['tm'][$technologyId][$projectId])) {
                    $data['technologies']['tm'][$technologyId][$projectId] = 0;
                }
                $data['technologies']['tm'][$technologyId][$projectId] += $data['engineers'][$engineerId][$projectId];
            }
        }
        dd($data);
dd($hours->groupBy(['planable_id', 'project_id']));

    }

    protected function detectTechnologyIds($data, $projectId, $technologies)
    {
        if ($technologies->count() > 1) {
            foreach ($technologies as $technology) {
                if (isset($data[$technology->technology_id][$projectId])) {
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
}
