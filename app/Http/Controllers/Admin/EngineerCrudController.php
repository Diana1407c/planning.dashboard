<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Operations\UpdateEngineerOperation;
use App\Jobs\SyncTeamworkEngineers;
use App\Models\EngineerHistory;
use App\Models\Team;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
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
    use UpdateEngineerOperation;

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
            'type' => 'model_function',
            'function_name' => 'levelName',
        ]);

        CRUD::addColumn([
            'label' => 'Performance(%)',
            'name'  => 'performance_value',
            'type' => 'model_function',
            'function_name' => 'displayPerformance',
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
//    protected function setupUpdateOperation()
//    {
//        $this->addLevelFields();
//        CRUD::setValidation(EngineerRequest::class);
//
//        Widget::add([
//            'type' => 'view',
//            'name' => 'engineer_history',
//            'label' => 'Engineer History',
//            'view' => 'admin.engineer_history',
//            'data' => [
//                'engineer' => $this->crud->getCurrentEntry(),
//                'histories' => $this->fetchEngineerHistory($this->crud->getCurrentEntryId()),
//            ],
//        ])->to('after_content');
//    }


    protected function fetchEngineerHistory(): void
    {
        $engineer = $this->crud->getCurrentEntry();
        $histories = EngineerHistory::where('engineer_id', $engineer->getKey())->orderBy('created_at', 'desc')->get();

        $this->data['engineer'] = $engineer;
        $this->data['histories'] = $histories;
    }
}
