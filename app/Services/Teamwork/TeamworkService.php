<?php

namespace App\Services\Teamwork;

use App\Models\Engineer;
use App\Models\Project;
use App\Models\TeamworkTime;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TeamworkService
{
    public static function getLoggedTime($filters): Collection|array
    {
        $query = TeamworkTime::query()
            ->select(
                'engineers.id as engineer_id',
                'engineers.first_name',
                'engineers.last_name',
                'technologies.id as technology_id',
                'stacks.id as stack_id'
            )
            ->selectRaw('YEAR(teamwork_time.date) as year, MONTH(teamwork_time.date) as month, sum(teamwork_time.hours) as hours')
            ->join('engineers', 'engineers.id', '=', 'teamwork_time.engineer_id')
            ->join('teams', 'teams.id', '=', 'engineers.team_id')
            ->join('technologies', 'technologies.id', '=', 'teams.technology_id')
            ->join('stacks', 'stacks.id', '=', 'technologies.stack_id')
            ->groupBy(['stacks.id', 'technologies.id', 'engineers.id', 'engineers.first_name', 'engineers.last_name', 'year', 'month']);

        if($filters['type'] == 'billable'){
            $query->where('teamwork_time.billable', true);
        } else if($filters['type'] == 'non_billable') {
            $query->where('teamwork_time.billable', false);
        }

        if($filters['engineer_ids']){
            $query->whereIn('engineers.id', $filters['engineer_ids']);
        }

        if($filters['technology_ids']){
            $query->whereIn('technologies.id', $filters['technology_ids']);
        }

        if($filters['stack_ids']){
            $query->whereIn('stacks.id', $filters['stack_ids']);
        }

        if($range = $filters['range']){
            if(isset($range['start_month'])){
                $query->where(function($subQuery) use($range){
                    $subQuery->where(function($andQuery) use($range){
                        $andQuery->whereRaw('MONTH(teamwork_time.date) >= '.$range['start_month'])
                            ->whereRaw('YEAR(teamwork_time.date) = '.$range['start_year']);
                    });
                    $subQuery->orWhereRaw("YEAR(teamwork_time.date) > ".$range['start_year']);
                });
            }

            if(isset($range['end_month'])){
                $query->where(function($subQuery) use($range){
                    $subQuery->where(function($andQuery) use($range){
                        $andQuery->whereRaw('MONTH(teamwork_time.date) <= '.$range['end_month'])
                            ->whereRaw('YEAR(teamwork_time.date) = '.$range['end_year']);
                    });
                    $subQuery->orWhereRaw('YEAR(teamwork_time.date) < '.$range['end_year']);
                });
            }
        }

        return $query->get();
    }

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
