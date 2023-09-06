<?php

namespace App\Http\Controllers\Admin\Operations;

use App\Models\Engineer;
use App\Models\Level;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

trait UpdateEngineerOperation
{
    protected function setupUpdateRoutes($segment, $routeName, $controller)
    {
        Route::get($segment . '/{id}/edit', [
            'as' => $routeName . '.edit',
            'uses' => $controller . '@edit',
            'operation' => 'update',
        ])->middleware('inertia:inertia');
    }

    protected function setupUpdateDefaults()
    {
        $this->crud->allowAccess('update');

        $this->crud->operation('update', function () {
            $this->crud->loadDefaultOperationSettingsFromConfig();

            if ($this->crud->getModel()->translationEnabled()) {
                $this->crud->addField([
                    'name' => '_locale',
                    'type' => 'hidden',
                    'value' => request()->input('_locale') ?? app()->getLocale(),
                ]);
            }

            $this->crud->setupDefaultSaveActions();
        });

        $this->crud->operation(['list', 'show'], function () {
            $this->crud->addButton('line', 'update', 'view', 'crud::buttons.update', 'end');
        });
    }

    public function edit(Request $request, $id)
    {
        $this->crud->hasAccessOrFail('update');
        // get entry ID from Request (makes sure its the last ID for nested resources)
        $id = $this->crud->getCurrentEntryId() ?? $id;
        // get the info for that entry

        /** @var Engineer $engineer */
        $engineer = $this->crud->getEntryWithLocale($id);

        return Inertia::render('EditEngineer', [
            'engineer' => $engineer,
            'levels' => Level::all(),
            'projects' => Project::all()->whereNotIn('state',Project::STATE_ARCHIVED),
        ])->withViewData([
            'title' => 'Edit ' . $engineer->fullName(),
            'breadcrumbs' => [
                trans('backpack::crud.admin') => backpack_url('dashboard'),
                'EditEngineer' => false,
            ],
        ]);
    }
}
