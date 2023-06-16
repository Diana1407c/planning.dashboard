<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UsersRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class UsersCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UsersCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/users');
        CRUD::setEntityNameStrings('users', 'users');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        $userCount = \App\Models\User::count();
            Widget::add([
                'type'        => 'progress',
                'class'       => 'card border-0 text-white bg-primary',
                'processClass'=> 'progress-bar',
                'value'       => $userCount,
                'description' => 'Registered users.',
                'progress'    => 100 * (int)$userCount / 1000,
                'hint'        => 1000 - $userCount . ' more until next milestone.',
           ]);

        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        CRUD::addColumn([
            'name'       => 'role',
            'type'      => 'select',
            'name'      => 'role_id',
            'entity'    => 'roles',
            'attribute' => 'name',
            'model'     => "app\Models\Role",
        ]);
        CRUD::addColumn([
            'name'         => 'permissions',
            'type'         => 'select_multiple',
            'label'        => 'Extra Permissions',
            'entity'       => 'permissions',
            'attribute'    => 'name',
            'model'        => "app\Models\Permission",
        ]);
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(UsersRequest::class);

        CRUD::field('name');
        CRUD::field('email');
        CRUD::addField([
            'name' => 'password',
            'label' => 'Password',
            'type' => 'password',
        ]);
        CRUD::addField([
            'name' => 'password_confirmation',
            'label' => 'Password Confirmation',
            'type' => 'password',
        ]);
        CRUD::addField([
            'label'             => 'User Role Permissions',
            'field_unique_name' => 'user_role_permission',
            'type'              => 'checklist_dependency',
            'name'              => ['roles', 'permissions'],
            'subfields'         => [
                'primary' => [
                    'label'            => 'Roles',
                    'name'             => 'roles',
                    'entity'           => 'roles',
                    'entity_secondary' => 'permissions',
                    'attribute'        => 'name',
                    'model'            => "app\Models\Role",
                    'pivot'            => true,
                    'number_columns'   => 3,
                ],
                'secondary' => [
                    'label'          => 'Permission',
                    'name'           => 'permissions',
                    'entity'         => 'permissions',
                    'entity_primary' => 'roles',
                    'attribute'      => 'name',
                    'model'          => "app\Models\Permission",
                    'pivot'          => true,
                    'number_columns' => 3,
                ],
            ],
    ]);

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $this->handlePasswordEncryption();

        return $this->traitStore();
    }

    public function update()
    {
        $this->handlePasswordEncryption();

        return $this->traitUpdate();
    }

    protected function handlePasswordEncryption()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $request = $this->crud->getRequest();

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', bcrypt($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        $this->crud->setRequest($request);
        $this->crud->unsetValidation();
    }
}
