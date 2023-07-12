<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EngineersExport implements FromCollection, WithHeadings
{
    protected Collection $engineers;

    public function __construct(Collection $engineers)
    {
        $this->engineers = $engineers;
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $this->engineers->each(function ($item){
            $item['hours'] = collect($item->toArray()['team_lead_plannings'])->sum('hours');
            $item->makeHidden(['id', 'team_id', 'user_id', 'created_at', 'updated_at', 'team_lead_plannings']);
        });
        return $this->engineers;
    }

    public function headings(): array
    {
        return [
            'first_name',
            'last_name',
            'email',
            'username',
            'hours'
        ];
    }
}
