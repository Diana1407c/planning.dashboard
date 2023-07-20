<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LevelRequest;
use App\Http\Resources\Short\ShortEngineersResource;
use App\Models\Engineer;
use App\Models\Level;
use App\Repositories\EngineerRepository;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Contracts\View\View;

/**
 * Class LevelCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LevelCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Level::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/level');
        CRUD::setEntityNameStrings('level', 'levels');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'label' => 'Level',
            'name' => 'name',

        ]);
        CRUD::addColumn([
            'label' => 'Performance(%)',
            'name' => 'performance',
            'type' => 'model_function',
            'function_name' => 'displayPerformance',
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->addLevelFields();
        CRUD::setValidation(LevelRequest::class);
    }

    protected function addLevelFields()
    {
        CRUD::addField([
            'name' => 'name',
            'label' => 'Level',
        ]);
        CRUD::addField([
            'name' => 'performance',
            'label' => 'Performance(%)',
            'type' => 'number',
            'attributes' => [
                'min' => 0,
                'max' => 100,
                'step' => 1,
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
