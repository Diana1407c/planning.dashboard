<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserStoreRequest as StoreRequest;
use App\Http\Requests\UserUpdateRequest as UpdateRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\RedirectResponse;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
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
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('user', 'users');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('email');
        CRUD::column('created_at');
        CRUD::column('updated_at');
        CRUD::addColumn([
            'name' => 'role',
            'type' => 'select',
            'entity' => 'roles',
            'attribute' => 'name',
            'model' => "App\Models\Role",
        ]);
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->addUserFields();
        CRUD::setValidation(StoreRequest::class);
        CRUD::addField([
            'label'      => 'Role',
            'type'       => 'select',
            'name'       => 'role',
            'entity'     => 'roles',
            'model'      => "App\Models\Role",
            'attribute'  => 'name',
            'pivot'      => true,
            'options'    => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
        ]);

    }
    /**
     * Define what happens when the Update operation is loaded.
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->addUserFields();
        CRUD::setValidation(UpdateRequest::class);

        $user = $this->crud->getCurrentEntry();
        $currentRole = $user->roles->first();

        CRUD::addField([
            'label'      => 'Role',
            'type'       => 'select',
            'name'       => 'role',
            'entity'     => 'roles',
            'model'      => "App\Models\Role",
            'attribute'  => 'name',
            'pivot'      => true,
            'options'    => function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            },
            'value'      => $currentRole->id,
        ]);
    }

    protected function handlePasswordEncryption()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $request = $this->crud->getRequest();

        if ($request->input('password')) {
            $request->request->set('password', bcrypt($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        $this->crud->setRequest($request);
        $this->crud->unsetValidation();
    }

    protected function addUserFields()
    {
        CRUD::field('name');
        CRUD::field('email');
        CRUD::addField([
            'name'      => 'password',
            'label'     => 'Password',
            'type'      => 'password',
        ]);
        CRUD::addField([
            'name'      => 'password_confirmation',
            'label'     => 'Password Confirmation',
            'type'      => 'password',
        ]);
    }

    protected function store(StoreRequest $request)
    {
        $this->handlePasswordEncryption();
        $user = $this->crud->create($request->except(['role']));
        $roles = $request->input('role', []);
        $user->roles()->sync($roles);
        return redirect()->route('user.index');
    }

    protected function update(UpdateRequest $request): RedirectResponse
    {
        $this->handlePasswordEncryption($request);
        $user = $this->crud->getCurrentEntry();
        $roles = $request->input('role', []);
        $user->roles()->sync($roles);
        return $this->crud->performSaveAction($this->crud->getCurrentEntry()->getKey());
    }
}
