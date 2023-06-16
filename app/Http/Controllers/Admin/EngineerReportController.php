<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Short\ShortProjectResource;
use App\Http\Resources\Short\ShortTeamsResource;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

/**
 * Class EngineerReportController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EngineerReportController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Reports/Engineers',[
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request),
            'allTeams' => ShortTeamsResource::collection(Team::all())->toArray($request)
        ])->withViewData([
            'title' => 'Engineers Report',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'EngineersReport' => false,
            ],
        ]);
    }
}
