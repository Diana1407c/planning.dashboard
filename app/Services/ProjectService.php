<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProjectService
{
    public static function filter(Request $request): Collection|array
    {
        $query = Project::query();

        if($project_ids = $request->get('project_ids')){
            $query->whereIn('id', $project_ids);
        }

        return $query->get();
    }
}
