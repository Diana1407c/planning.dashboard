<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Short\ShortProjectResource;
use App\Models\Project;
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
        return Inertia::render('Reports/Statistics',[
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request),
            'projectTypes' => Project::indexedTypes(),
        ])->withViewData([
            'title' => 'Statistics',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'ProjectHistoryReport' => false,
            ],
        ]);
    }
}
