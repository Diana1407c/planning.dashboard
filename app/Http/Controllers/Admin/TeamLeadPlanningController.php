<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

/**
 * Class TeamLeadPlanningController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TeamLeadPlanningController extends Controller
{
    public function index()
    {
        return Inertia::render('TLPlanning')
            ->withViewData([
                'title' => 'Team Lead Planning',
                'breadcrumbs' => [
                    trans('backpack::crud.admin') => backpack_url('dashboard'),
                    'TeamLeadPlanning' => false,
                ],
            ]);
    }
}
