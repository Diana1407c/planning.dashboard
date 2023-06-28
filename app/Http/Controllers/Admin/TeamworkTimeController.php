<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\StackResource;
use App\Http\Resources\StackTechnologiesEngineersResource;
use App\Http\Resources\TechnologyResource;
use App\Models\Stack;
use App\Models\Technology;
use App\Services\EngineerService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;

/**
 * Class TeamworkTimeController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TeamworkTimeController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Reports/TeamworkTime',[
            'allTechnologies' => TechnologyResource::collection(Technology::all())->toArray($request),
            'allStacks' => StackResource::collection(Stack::all())->toArray($request),
            'allEngineers' => StackTechnologiesEngineersResource::collection(EngineerService::withTeams())->toArray($request)
        ])->withViewData([
            'title' => 'Teamwork Time',
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'TeamworkTime' => false,
            ],
        ]);
    }
}
