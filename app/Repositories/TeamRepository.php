<?php

namespace App\Repositories;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use stdClass;

class TeamRepository
{
    public function all(Request $request): Collection|LengthAwarePaginator|array
    {
        $query = Team::query();

        if($request->has('filter')){
            $this->useFilter($query, $request->get('filter'));
        }

        if($request->has('per_page')){
            return $query->paginate($request->get('per_page'));
        }

        return $query->get();
    }

    public function create(TeamRequest $request): Builder|Model
    {
        return Team::query()->create($request->validated());
    }

    public function update(Team $team, TeamRequest $request): Team
    {
        $team->update($request->validated());
        return $team;
    }

    public function delete(Team $team): void
    {
        $team->delete();
    }

    private function useFilter(Builder &$query, StdClass $filter): void
    {

    }
}
