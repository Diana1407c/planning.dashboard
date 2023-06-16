<?php

namespace App\Services\Teamwork;

use App\Models\Engineer;
use App\Models\Project;
use App\Models\TeamworkTime;
use Carbon\Carbon;

class TeamworkService
{
    public static function syncEngineers(): void
    {
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

    public static function syncEngineer(int $id): void
    {
        $engineer = (new TeamworkProxy())->getPerson($id);
        if($engineer){
            $id = $engineer['id'];
            $data = array_diff_key($engineer, ['id' => '']);

            Engineer::query()->updateOrCreate(
                ['id' => $id],
                $data
            );
        }
    }

    public static function syncProjects(): void
    {
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

    public static function syncTimeEntries(Carbon $fromDate, Carbon $toDate): void
    {
        $entries = (new TeamworkProxy())->getTimeEntries($fromDate, $toDate);
        foreach ($entries as $entry) {
            $id = $entry['id'];
            $data = array_diff_key($entry, ['id' => '']);

            try {
                TeamworkTime::query()->updateOrCreate(
                    ['id' => $id],
                    $data
                );
            } catch (\Exception $exception){
                self::syncEngineer(intval($entry['engineer_id']));

                TeamworkTime::query()->updateOrCreate(
                    ['id' => $id],
                    $data
                );
            }

        }
    }
}
