<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Short\ShortProjectResource;
use App\Http\Resources\Short\ShortTeamsResource;
use App\Http\Resources\StackTechnologiesEngineersResource;
use App\Models\Engineer;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

/**
 * Class StatisticsController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        $engineers = Engineer::query()->whereNotNull('team_id')
            ->with(['team', 'team.technologies'])->get();

        return Inertia::render('Reports/Statistics', [
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request),
            'projectTypes' => Project::indexedTypes(),
            'projectStates' => Project::indexedStates(),
            'allTeams' => ShortTeamsResource::collection(Team::all())->toArray($request),
            'allEngineers' => StackTechnologiesEngineersResource::collection($engineers)->toArray($request)
        ])->withViewData([
            'title' => 'Statistics',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'ProjectHistoryReport' => false,
            ],
        ]);
    }
}
