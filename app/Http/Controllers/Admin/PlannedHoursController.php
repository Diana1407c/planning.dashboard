<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Short\ShortProjectResource;
use App\Http\Resources\Short\ShortTeamsResource;
use App\Http\Resources\TechnologyResource;
use App\Models\Project;
use App\Models\Team;
use App\Models\Technology;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

/**
 * Class PlannedHoursController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PlannedHoursController extends Controller
{
    public function tlWeekly(Request $request)
    {
        return Inertia::render('WeeklyTLPlanning',[
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request),
            'allTeams' => ShortTeamsResource::collection(Team::all())->toArray($request)
        ])->withViewData([
            'title' => 'Weekly Team Lead Planning',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'WeeklyTeamLeadPlanning' => false,
            ],
        ]);
    }

    public function tlMonthly(Request $request)
    {
        return Inertia::render('MonthlyTLPlanning',[
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request),
            'allTeams' => ShortTeamsResource::collection(Team::all())->toArray($request)
        ])->withViewData([
            'title' => 'Monthly Team Lead Planning',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'MonthlyTeamLeadPlanning' => false,
            ],
        ]);
    }

    public function pmMonthly(Request $request)
    {
        return Inertia::render('MonthlyPMPlanning',[
            'technologies' => TechnologyResource::collection(Technology::all())->toArray($request),
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request),
        ])->withViewData([
            'title' => 'Monthly Project Manager Planning',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'MonthlyProjectManagerPlanning' => false,
            ],
        ]);
    }

    public function pmWeekly(Request $request)
    {
        return Inertia::render('WeeklyPMPlanning',[
            'technologies' => TechnologyResource::collection(Technology::all())->toArray($request),
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request),
        ])->withViewData([
            'title' => 'Weekly Project Manager Planning',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'WeeklyProjectManagerPlanning' => false,
            ],
        ]);
    }
}
