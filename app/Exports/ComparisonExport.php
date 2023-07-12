<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ComparisonExport implements FromView
{
    protected array $comparisonData;

    public function __construct(array $comparisonData)
    {
        $this->comparisonData = $comparisonData;
    }

    public function view(): View
    {
        return view('exports.comparison', $this->comparisonData);
    }
}
