<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EngineerRequest;
use App\Jobs\SyncTeamworkEngineers;
use App\Models\Team;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

/**
 * Class EngineerCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EngineerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Engineer::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/engineer');
        CRUD::setEntityNameStrings('engineer', 'engineers');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        Widget::add([
            'type'    => 'button',
            'label'    => 'Sync Engineers',
            'route'    => $this->crud->route.'/sync',
        ]);

        CRUD::column('first_name');
        CRUD::column('last_name');
        CRUD::column('email');
        CRUD::column('username');

        CRUD::addColumn([
            'label' => 'Level',
            'name'  => 'level_id',
            'entity' => 'level',
            'attribute' => 'name',
            'model' => Level::class
        ]);

        CRUD::addColumn([
            'label' => 'Team',
            'name'  => 'team_id',
            'entity' => 'team',
            'attribute' => 'name',
            'model' => Team::class
        ]);

        CRUD::addColumn([
            'label' => 'Related user',
            'name'  => 'user_id',
            'entity' => 'user',
            'attribute' => 'name',
            'model' => User::class
        ]);

    }

    public function sync(): Application|\Illuminate\Routing\Redirector|\Illuminate\Contracts\Foundation\Application|RedirectResponse
    {
        SyncTeamworkEngineers::dispatch();

        return redirect($this->crud->route);
    }
}
