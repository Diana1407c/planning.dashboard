<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeamRequest;
use App\Http\Resources\Short\ShortEngineersResource;
use App\Http\Resources\TechnologyResource;
use App\Models\Engineer;
use App\Models\Team;
use App\Models\Technology;
use App\Repositories\EngineerRepository;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class TeamCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TeamCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Team::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/team');
        CRUD::setEntityNameStrings('team', 'teams');

    }

    protected function basicCreateUpdate()
    {
        CRUD::setValidation(TeamRequest::class);

        CRUD::field('name');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');

        CRUD::addColumn([
            'label' => 'Team Lead',
            'name'  => 'team_lead_id',
            'entity' => 'teamLead',
            'attribute' => 'first_name',
            'model' => Engineer::class
        ]);

        CRUD::addColumn([
            'label' => 'Members',
            'name'  => 'members',
            'type'  => 'model_function',
            'function_name' => 'membersCount'
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->basicCreateUpdate();

        $allEngineers = ShortEngineersResource::collection(EngineerRepository::all());
        $select = '<label>Team Lead</label><select name="team_lead_id" class="form-control">';
        foreach ($allEngineers as $engineer){
            $select .= '<option value="'. $engineer->id .'">'.$engineer->fullName().' - '. $engineer->email .'</option>';
        }
        $select .= '</select>';

        CRUD::addField([
            'label' => "Team Lead",
            'name'  => 'team_lead_id',
            'type'  => 'custom_html',
            'value' => $select
        ]);

        $technologies = TechnologyResource::collection(Technology::all());
        $withoutTeam = ShortEngineersResource::collection(EngineerRepository::withoutTeam());

        CRUD::addField([
            'name'  => 'technologies',
            'type'  => 'custom_html',
            'value' => '<div id="backpack-vue">
                    <custom-fields-team :engineers=\''.json_encode($withoutTeam).'\' :technologies=\''.json_encode($technologies).'\'> </custom-fields-team>
            </div>'
        ]);
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->basicCreateUpdate();
        $withoutTeam = ShortEngineersResource::collection(EngineerRepository::withoutTeam());
        $currentTeam = ShortEngineersResource::collection(EngineerRepository::team($this->crud->getCurrentEntryId()));

        CRUD::addField([
            'label'     => "Team Lead",
            'type'      => 'select',
            'name'      => 'team_lead_id',
            'entity'    => 'teamLead',
            'model'     => "App\Models\Engineer",
        ]);

        $forSelecting = $withoutTeam->concat($currentTeam)->unique(function ($item) {
            return $item['id'];
        });

        $technologies = TechnologyResource::collection(Technology::all());
        $currentTechnologies = TechnologyResource::collection($this->crud->getCurrentEntry()->technologies);

        CRUD::addField([
            'name'  => 'technologies',
            'type'  => 'custom_html',
            'value' => '<div id="backpack-vue">
                    <custom-fields-team :current_members=\''.json_encode($currentTeam).'\' :current_technologies=\''.json_encode($currentTechnologies).'\'
                    :engineers=\''.json_encode($forSelecting).'\' :technologies=\''.json_encode($technologies).'\'> </custom-fields-team>
            </div>'
        ]);

        CRUD::addField([
            'name'  => 'separator',
            'type'  => 'custom_html',
            'value' => '<div id="backpack-vue">
                            <engineer-multiselect :engineers=\''.json_encode($forSelecting).'\' :selected-props=\''.json_encode($currentTeam).'\'></engineer-multiselect>
                        </div>'
        ]);
    }

    public function store(TeamRequest $request): View
    {
        /** @var Team $team */
        $team = Team::query()->create($request->validated());
        $team->technologies()->sync($request->get('technologies'));

        $this->storeMembers($team, $request->get('members'));

        return view($this->crud->getCreateView(), $this->data+['crud' => $this->crud]);
    }

    public function update(TeamRequest $request): RedirectResponse
    {
        $team = Team::find($request->get('id'));
        $team->update($request->validated());
        $team->technologies()->sync($request->get('technologies'));
        $this->storeMembers($team, $request->get('members'));

        return $this->crud->performSaveAction($this->crud->getCurrentEntry()->getKey());
    }

    protected function storeMembers(Team $team, array $members)
    {
        $deletedMembers = $team->members()->whereNotIn('id', $members)->get();

        $newMembers = Engineer::query()->whereIn('id', $members)
            ->where(function ($q) use($team) {
                $q->where('team_id', '<>', $team->id)
                    ->orWhereNull('team_id');
            })->get();

        foreach ($newMembers as $member) {
            $member->team_id = $team->id;
            $member->save();
        }

        foreach ($deletedMembers as $member) {
            $member->team_id = null;
            $member->save();
        }
    }
}
