<?php

namespace App\Services\Teamwork;

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
                    'name' => $result->name,
                    'status' => $result->status
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
}
