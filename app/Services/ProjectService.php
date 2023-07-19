<?php

namespace App\Services;

use App\Http\Requests\Planning\PlanningFilterInterface;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ProjectService
{
    public static function filter(Request|PlanningFilterInterface|array $filter): Collection|array
    {
        $query = Project::query();

        if (!empty($filter['project_ids'])) {
            $query->whereIn('id', $filter['project_ids']);
        }

        return $query->get();
    }
}
