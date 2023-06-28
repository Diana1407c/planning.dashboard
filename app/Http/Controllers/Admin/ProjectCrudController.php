<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SyncTeamworkProjects;
use App\Models\Project;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

/**
 * Class ProjectCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ProjectCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Project::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/project');
        CRUD::setEntityNameStrings('project', 'projects');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        Widget::add([
            'type'    => 'button',
            'label'    => 'Sync Projects',
            'route'    => $this->crud->route.'/sync',
        ]);

        CRUD::column('name');
        CRUD::column('state');
    }

    protected function setupUpdateOperation()
    {
        CRUD::addField([
            'label'     => "State",
            'type'      => 'select_from_array',
            'name'      => 'state',
            'allows_null' => false,
            'default'     => Project::STATE_ACTIVE,
            'options'   => [
                Project::STATE_ACTIVE => 'Active',
                Project::STATE_MAINTENANCE => 'Maintenance',
                Project::STATE_OPERATIONAL => 'Operational'
            ],
        ]);
    }

    public function sync(): Application|\Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        SyncTeamworkProjects::dispatch();

        return redirect($this->crud->route);
    }
}
