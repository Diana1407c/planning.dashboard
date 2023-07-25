<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\HolidayRequest;
use App\Models\Holiday;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class LevelCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class HolidayCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Holiday::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/holidays');
        CRUD::setEntityNameStrings('holiday', 'holidays');
    }

    protected function setupListOperation()
    {
        CRUD::addColumn([
            'label' => 'Name',
            'name' => 'name',
        ]);

        CRUD::addColumn([
            'label' => 'Date',
            'name' => 'date',
            'type' => 'date',
        ]);

        CRUD::addColumn([
            'label' => 'Type',
            'name' => 'type',
        ]);
    }

    protected function setupCreateOperation()
    {
        $this->addFields();
        CRUD::setValidation(HolidayRequest::class);
    }

    protected function addFields()
    {
        Widget::add()->type('script')->content('/js/holiday.js');

        CRUD::addField([
            'name' => 'name',
            'label' => 'Name',
        ]);

        CRUD::addField([
            'name' => 'date',
            'label' => 'Date',
            'type' => 'date',
        ]);

        CRUD::addField([
            'label'     => "Type",
            'type'      => 'select_from_array',
            'name'      => 'type',
            'allows_null' => false,
            'default'     => Holiday::HOLIDAY_TYPE,
            'options'   => [
                Holiday::HOLIDAY_TYPE => Holiday::HOLIDAY_TYPE,
                Holiday::SHORT_TYPE => Holiday::SHORT_TYPE,
                Holiday::DAY_OFF_TYPE => Holiday::DAY_OFF_TYPE,
                Holiday::RECOVERABLE_TYPE => Holiday::RECOVERABLE_TYPE,
            ],
        ]);

        CRUD::addField([
            'label'     => "Every year",
            'type'      => 'checkbox',
            'name'      => 'every_year',
            'allows_null' => false,
            'default'     => false,
        ]);

        CRUD::addField([
            'name' => 'day_hours',
            'label' => 'Day hours',
            'type' => 'number',
            'attributes' => [
                'min' => 1,
                'max' => 10,
                'step' => 1,
            ],
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
