<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Short\ShortProjectResource;
use App\Http\Resources\Short\ShortTeamsResource;
use App\Models\Engineer;
use App\Models\Project;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

/**
 * Class TeamLeadPlanningController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TeamLeadPlanningController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('TLPlanning',[
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request),
            'allTeams' => ShortTeamsResource::collection(Team::all())->toArray($request)
        ])->withViewData([
                'title' => 'Team Lead Planning',
                'breadcrumbs' => [
                    trans('backpack::crud.admin') => backpack_url('dashboard'),
                    'TeamLeadPlanning' => false,
                ],
            ]);
    }
}
