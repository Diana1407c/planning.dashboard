<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\TeamRequest;
use App\Http\Resources\Short\ShortEngineersResource;
use App\Models\Engineer;
use App\Models\EngineerHistory;
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

        CRUD::addField([
            'label' => 'Technology',
            'type'  => 'select',
            'name'  => 'technology_id',
            'entity' => 'technology',
            'attribute' => 'name',
            'model' => Technology::class
        ]);
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
            'label' => 'Technology',
            'name'  => 'technology_id',
            'entity' => 'technology',
            'attribute' => 'name',
            'model' => Technology::class
        ]);

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

        $withoutTeam = ShortEngineersResource::collection(EngineerRepository::withoutTeam());

        $select = '<label>Team Lead</label><select name="team_lead_id" class="form-control">';
        foreach ($withoutTeam as $engineer){
            $select .= '<option value="'. $engineer->id .'">'.$engineer->fullName().' - '. $engineer->email .'</option>';
        }
        $select .= '</select>';

        CRUD::addField([
            'label' => "Team Lead",
            'name'  => 'team_lead_id',
            'type'  => 'custom_html',
            'value' => $select
        ]);

        CRUD::addField([
            'name'  => 'separator',
            'type'  => 'custom_html',
            'value' => '<div id="backpack-vue">
                            <engineer-multiselect :engineers=\''.json_encode($withoutTeam).'\'> </engineer-multiselect>
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

        $currentMembersId = (clone $currentTeam)->pluck('id')->toArray();
        $currentLead = $this->crud->getCurrentEntry()->team_lead_id;

        if (!in_array($currentLead, $currentMembersId)) {
            $currentMembersId[] = $currentLead;
        }

        CRUD::addField([
            'label'     => "Team Lead",
            'type'      => 'select',
            'name'      => 'team_lead_id',
            'entity'    => 'teamLead',
            'model'     => "App\Models\Engineer",
            'options'   => (function ($query) use($currentMembersId) {
                return $query
                    ->whereNull('team_id')
                    ->orWhereIn('id', $currentMembersId)->get();
            }),
        ]);

        $forSelecting = $withoutTeam->concat($currentTeam)->unique(function ($item) {
            return $item['id'];
        });

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
        $team = Team::query()->create($request->validated());

        $membersIds = explode('|', $request->get('members',));
        if (!in_array($team->team_lead_id, $membersIds)) {
            $membersIds[] = $team->team_lead_id;
        }

        Engineer::query()->whereIn('id', $membersIds)->update(['team_id' => $team->id]);

        foreach ($membersIds as $memberId) {
            if ($memberId !== $team->team_lead_id) {
                $member = Engineer::find($memberId);
                EngineerHistory::create([
                    'engineer_id' => $member->id,
                    'historyable_type' => 'team',
                    'historyable_id' => $team->id,
                    'value' => $team->name,
                    'label' => 'engineer_added_to_team',
                ]);
            }
        }

        return view($this->crud->getCreateView(), $this->data+['crud' => $this->crud]);
    }

    public function update(TeamRequest $request): RedirectResponse
    {
        $team = Team::find($request->get('id'));
        $team->update($request->validated());
        $oldMembersIds = $team->members()->pluck('id')->toArray();

        $membersIds = explode('|', $request->get('members',));
        if (!in_array($team->team_lead_id, $membersIds)) {
            $membersIds[] = $team->team_lead_id;
        }
        $currentMembersIds = Engineer::whereIn('id', $membersIds)->pluck('id')->toArray();

        $membersToAdd = array_diff($currentMembersIds, $oldMembersIds);
        $membersToRemove = array_diff($oldMembersIds, $currentMembersIds);

        if($membersToAdd){
            Engineer::query()->whereIn('id', $membersToAdd)->update(['team_id' => $team->id]);

            foreach ($membersToAdd as $memberId) {
                $member = Engineer::find($memberId);
                EngineerHistory::create([
                    'engineer_id' => $member->id,
                    'historyable_type' => 'team',
                    'historyable_id' => $team->id,
                    'value' => $team->name,
                    'label' => 'engineer_added_to_team',
                ]);
            }
        }
        if($membersToRemove){
            Engineer::query()->whereIn('id', $membersToRemove)->update(['team_id' => null]);

            foreach ($membersToRemove as $memberId) {
                EngineerHistory::create([
                    'engineer_id' => $memberId,
                    'historyable_type' => 'team',
                    'historyable_id' => $team->id,
                    'value' => $team->name,
                    'label' => 'engineer_deleted_from_team',
                ]);
            }
        }
        return $this->crud->performSaveAction($this->crud->getCurrentEntry()->getKey());
    }
}
