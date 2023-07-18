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
            'label' => 'Engineer',
            'name'  => 'engineer_id',
            'type' => 'select',
            'entity' => 'engineer',
            'attribute' =>'fullName',
            'model' => Engineer::class,
        ]);

        CRUD::addColumn([
            'label' => 'Level',
            'name'  => 'name',

        ]);

        CRUD::column('performance');

    }

    protected function setupCreateOperation()
    {
        $withoutLevel = ShortEngineersResource::collection(EngineerRepository::withoutLevel());

        $select = '<label>Engineer</label><select name="engineer_id" class="form-control">';
        foreach ($withoutLevel as $engineer){
            $select .= '<option value="'. $engineer->id .'">'.$engineer->fullName().' - '. $engineer->email .'</option>';
        }
        $select .= '</select>';

        CRUD::addField([
            'label' => "Engineer",
            'name'  => 'engineer_id',
            'type'  => 'custom_html',
            'value' => $select
        ]);

        $this->addLevelFields();

        CRUD::setValidation(LevelRequest::class);
    }

    protected function addLevelFields()
    {
        CRUD::addField([
            'name' => 'name',
            'label' => 'Level',
            'type' => 'enum',
            'options' => ['junior' => 'Junior', 'middle' => 'Middle', 'senior' => 'Senior']
        ]);

        CRUD::addField([
            'name' => 'performance',
            'label' => 'Performance',
            'type' => 'number',
            'attributes' => [
                'min' => 0,
                'max' => 100,
                'step' => 1,
            ],
        ]);
    }

    public function store(LevelRequest $request): View
    {
        $level = $this->crud->create($request->validated());
        $engineerId = $request->input('engineer_id');

        $engineer = Engineer::find($engineerId);
        $engineer->level_id = $level->id;
        $engineer->save();

        return view($this->crud->getCreateView(), $this->data+['crud' => $this->crud]);
    }

    protected function setupUpdateOperation()
    {
        $currentEngineer = $this->crud->getCurrentEntry()->engineer_id;
        $engineer = Engineer::find($currentEngineer);

        $customField = '<label>Engineer</label><input type="text" value="' . $engineer->fullName() . '" class="form-control" disabled>';

        CRUD::addField([
            'name' => 'engineer',
            'type' => 'custom_html',
            'value' => $customField,
        ]);

        $this->addLevelFields();
    }
}
