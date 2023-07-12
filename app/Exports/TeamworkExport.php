<?php

namespace App\Exports;

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
        return view('exports.teamwork', $this->teamworkData);
    }
}
