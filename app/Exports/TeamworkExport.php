<?php

namespace App\Exports;

use App\Models\Stack;
use App\Models\Technology;
use App\Services\EngineerService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TeamworkExport implements FromView
{
    protected array $teamworkData;

    public function __construct(array $teamworkData)
    {
        $this->teamworkData = $teamworkData;
    }

    public function view(): View
    {
        return view('exports.teamwork', $this->teamworkData+[
                'technologies' => Technology::all()->pluck('name', 'id'),
                'stacks' => Stack::all()->pluck('name', 'id'),
                'engineers' => EngineerService::withTeams()->pluck('name', 'id')
            ]);
    }
}
