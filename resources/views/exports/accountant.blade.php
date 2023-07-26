<table>
    <thead>
    <tr>
        <td>Engineers</td>
        <td>Performance</td>
        <td>Team</td>
        <td>Technology</td>
        <td>Seniority</td>
        @foreach($statuses as $state)
            @if(!empty($projects[$state]))
                @foreach($projects[$state] as $project)
                    <td>{{ $project->name }}</td>
                @endforeach
            @endif
            <td>{{ $state }}</td>
        @endforeach
        <td>Total Planned</td>
        <td>Unplanned</td>
    </tr>
    </thead>
    <tbody>
    @foreach($engineers as $engineer)
        <tr>
            <td>{{ $engineer->fullName() }}</td>
            <td>{{ $engineer->performancePercent() }}%</td>
            <td>{{ $engineer->team->name }}</td>
            <td>
                @if($engineer->team->technologies)
                    {{ implode(', ', $engineer->team->technologies->pluck('name')->toArray()) }}
                @endif
            </td>
            <td>
                @if($engineer->level)
                    {{ $engineer->level->name }}
                @endif
            </td>

            @foreach($statuses as $state)
                @if(!empty($projects[$state]))
                    @foreach($projects[$state] as $project)
                        <td>{{ $matrix['engineers'][$engineer->id][$project->id] }}</td>
                    @endforeach
                @endif
                <td>{{ $matrix['engineers'][$engineer->id]['total'][$state] }}</td>
            @endforeach
            <td>{{ $matrix['engineers'][$engineer->id]['total']['all'] }}</td>
            <td>{{ $matrix['engineers'][$engineer->id]['total']['unplanned'] }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="5">Totals</td>
        @foreach($statuses as $state)
            @if(!empty($projects[$state]))
                @foreach($projects[$state] as $project)
                    <td>{{ $matrix['projects'][$project->id] }}</td>
                @endforeach
            @endif
            <td>{{ $matrix['projects'][$state] }}</td>
        @endforeach
        <td>{{ $matrix['projects']['all'] }}</td>
        <td>{{ $matrix['projects']['unplanned'] }}</td>
    </tr>
    </tbody>
</table>
