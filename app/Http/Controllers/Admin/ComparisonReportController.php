<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Short\ShortProjectResource;
use App\Models\Project;
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
        return Inertia::render('Reports/Comparison',[
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request),
            'projectStates' => Project::indexedStates()
        ])->withViewData([
            'title' => 'Comparison Report',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'ComparisonPlanning' => false,
            ],
        ]);
    }
}
