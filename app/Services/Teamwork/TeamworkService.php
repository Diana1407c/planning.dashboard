<?php

namespace App\Services\Teamwork;

use App\Models\Engineer;
use App\Models\Project;

class TeamworkService
{
    public static function syncEngineers(){
        $engineers = (new TeamworkProxy())->getPeople();

        foreach ($engineers as $engineer) {
            $id = $engineer['id'];
            $data = array_diff_key($engineer, ['id' => '']);

            Engineer::query()->updateOrCreate(
                ['id' => $id],
                $data
            );
        }
    }

    public static function syncProjects(){
        $projects = (new TeamworkProxy())->getProjects();

        foreach ($projects as $project) {
            $id = $project['id'];
            $data = array_diff_key($project, ['id' => '']);

            Project::query()->updateOrCreate(
                ['id' => $id],
                $data
            );
        }
    }
}
