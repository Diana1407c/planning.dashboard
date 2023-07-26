<?php

namespace App\Exports;

use App\Models\Project;
use App\Services\EngineerService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AccountantExport implements FromView
{
    protected array $matrix;
    protected $engineers;
    protected $projects;
    protected $statuses;

    public function __construct(array $matrix)
    {
        $this->matrix = $matrix;
        $this->statuses = Project::states();
        $this->projects = Project::all()->groupBy('state');
        $this->engineers = (new EngineerService())->accountantEngineers([]);
    }

    public function view(): View
    {
        return view('exports.accountant', [
            'matrix' => $this->matrix,
            'engineers' => $this->engineers,
            'projects' => $this->projects,
            'statuses' => $this->statuses,
        ]);
    }
}
