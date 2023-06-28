<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Short\ShortProjectResource;
use App\Http\Resources\StackResource;
use App\Models\Project;
use App\Models\Stack;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

/**
 * Class ProjectManagerPlanningController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProjectManagerPlanningController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('PMPlanning', [
            'stacks' => StackResource::collection(Stack::all())->toArray($request),
            'allProjects' => ShortProjectResource::collection(Project::all())->toArray($request)
        ])->withViewData([
                'title' => 'Project Manager Planning',
                'breadcrumbs' => [
                    trans('backpack::crud.admin') => backpack_url('dashboard'),
                    'ProjectManagerPlanning' => false,
                ],
            ]);
    }
}
