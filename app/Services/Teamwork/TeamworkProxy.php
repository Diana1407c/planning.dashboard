<?php

namespace App\Services\Teamwork;

use Carbon\Carbon;
use GuzzleHttp\Exception\GuzzleException;

class TeamworkProxy
{
    protected TeamworkApi $api;

    public function __construct()
    {
        $this->api = new TeamworkApi();
    }

    public function getProjects(): ?array
    {
        try {
            $results = $this->api->getProjects();
            $projects = [];
            foreach ($results->projects as $result){
                $projects[] = [
                    'id' => $result->id,
                    'name' => $result->name
                ];
            }

            return $projects;
        } catch (\Exception|GuzzleException $exception) {
            return null;
        }
    }

    public function getPeople(): ?array
    {
        try {
            $results = $this->api->getPeople();
            $people = [];
            foreach ($results['people'] as $person){
                if($person['user-type'] == 'account'){
                    $people[] = [
                        'id' => $person['id'],
                        'first_name' => $person['first-name'],
                        'last_name' => $person['last-name'],
                        'email' => $person['email-address'],
                        'username' => $person['user-name']
                    ];
                }
            }

            return $people;
        } catch (\Exception|GuzzleException $exception) {
            return null;
        }
    }

    public function getPerson(int $id): ?array
    {
        try {
            $results = $this->api->getPerson($id);
            if($teamworkPerson = $results['person']){
                return [
                    'id' => $teamworkPerson['id'],
                    'first_name' => $teamworkPerson['first-name'],
                    'last_name' => $teamworkPerson['last-name'],
                    'email' => $teamworkPerson['email-address'],
                    'username' => $teamworkPerson['user-name']
                ];
            }

            return null;
        } catch (\Exception|GuzzleException $exception) {
            return null;
        }
    }

    public function getTimeEntries(Carbon $fromDate, Carbon $toDate): ?array
    {
        try {
            $entries = [];
            $page = 1;
            $results = $this->api->getTimeEntries($fromDate, $toDate, $page);
            while(!empty($results['time-entries'])){
                foreach ($results['time-entries'] as $entry){
                    $entries[] = [
                        'id' => $entry['id'],
                        'engineer_id' => $entry['person-id'],
                        'hours' => $entry['hoursDecimal'],
                        'date' => Carbon::parse($entry['date'])->toDate(),
                        'billable' => filter_var($entry['isbillable'],FILTER_VALIDATE_BOOLEAN)
                    ];
                }
                $page++;
                $results = $this->api->getTimeEntries($fromDate, $toDate, $page);
            }

            return $entries;
        } catch (\Exception|GuzzleException $exception) {
            return [];
        }
    }
}
