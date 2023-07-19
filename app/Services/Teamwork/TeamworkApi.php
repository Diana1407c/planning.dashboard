<?php

namespace App\Services\Teamwork;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use stdClass;
use function config;

class TeamworkApi
{
    protected string $domain;

    protected string $apiKey;

    protected Client $client;

    public function __construct()
    {
        $this->domain = config('services.teamwork.domain');
        $this->apiKey = base64_encode(config('services.teamwork.email').':'.config('services.teamwork.password'));
        $this->client = new Client();
    }

    /**
     * @return StdClass
     * @throws GuzzleException
     */
    public function getProjects(): StdClass
    {
        $response = $this->client->get($this->url('/projects.json'), [
            'headers' => $this->headers()
        ]);

        return json_decode($response->getBody()->getContents());
    }

    /**
     * @throws GuzzleException
     */
    public function getPeople(): array
    {
        $response = $this->client->get($this->url('/people.json'), [
            'headers' => $this->headers(),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws GuzzleException
     */
    public function getTimeEntries(Carbon $fromDate, Carbon $toDate, int $page = 1): array
    {
        $response = $this->client->get($this->url("/time_entries.json"), [
            'query'=> [
                'page' => $page,
                'fromdate' => $fromDate->format('Ymd'),
                'todate' => $toDate->format('Ymd')
            ],
            'headers' => $this->headers(),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @throws GuzzleException
     */
    public function getPerson(int $id): array
    {
        $response = $this->client->get($this->url("/people/$id.json"), [
            'headers' => $this->headers(),
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getProject(int $id): array
    {
        $response = $this->client->get($this->url("/projects/$id.json"), [
            'headers' => $this->headers()
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return string[]
     */
    public function headers(): array
    {
        return [
            'Authorization' => 'Basic ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @param string $path
     * @return string
     */
    public function url(string $path): string
    {
        return $this->domain . $path;
    }
}
