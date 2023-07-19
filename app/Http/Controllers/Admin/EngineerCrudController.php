<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\EngineerRequest;
use App\Jobs\SyncTeamworkEngineers;
use App\Models\Level;
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
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

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
            'label' => 'Performance(%)',
            'name'  => 'performance',
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
    protected function setupUpdateOperation()
    {
        $this->addLevelFields();
        CRUD::setValidation(EngineerRequest::class);
    }

    protected function addLevelFields()
    {
        CRUD::field('first_name');
        CRUD::field('last_name');
        CRUD::field('email');
        CRUD::field('username');

        $engineer = $this->crud->getCurrentEntry();
        $teamName = $engineer->team ? $engineer->team->name : '-';
        $userName = $engineer->user ? $engineer->user->name : '-';

        CRUD::addField([
            'name' => 'level_id',
            'label' => 'Level',
            'type' => 'select',
            'entity' => 'level',
            'attribute' => 'name',
            'model' => Level::class,
            'value' => $engineer->level_id,

        ]);

        CRUD::addField([
            'name' => 'performance',
            'label' => 'Individual performance(%)',
            'type' => 'number',
            'attributes' => [
                'min' => 0,
                'max' => 100,
                'step' => 1,
            ],
        ]);

        CRUD::addField([
            'name' => 'team_id',
            'label' => 'Team ',
            'type' => 'text',
            'model' => Team::class,
            'value' => $engineer->team_id ? $engineer->team->name : '-',
            'attributes' => [
                'readonly' => true,
            ],
        ]);

        CRUD::addField([
            'name' => 'user_id',
            'label' => 'Related user ',
            'type' => 'text',
            'model' => User::class,
            'value' => $engineer->user_id ? $engineer->user->name : '-',
            'attributes' => [
                'readonly' => true,
            ],
        ]);

        CRUD::setValidation(EngineerRequest::class);
    }
    public function update(EngineerRequest $request): RedirectResponse
    {
        $engineer = $this->crud->getCurrentEntry();
        $engineer->update($request->except(['level_id', 'performance']));
        $engineer->level_id = $request->input('level_id');
        $engineer->performance = $request->input('performance');
        $engineer->save();

        return $this->crud->performSaveAction($this->crud->getCurrentEntry()->getKey());
    }
}
