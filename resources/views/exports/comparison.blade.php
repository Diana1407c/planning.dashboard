<table>
    <thead>
    <tr>
        <th rowspan="2">Projects</th>
        @foreach($dates as $date)
            <th colspan="2">{{ $date }}</th>
        @endforeach
    </tr>
    <tr>
        @foreach($dates as $date)
            <th>PM</th>
            <th>TL</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($projects as $project)
        <tr>
            <td>{{ $project['name'] }}</td>
            @foreach($dates as $index => $date)
                <td>{{ $report[$project['id']][$index]['PM'] }}</td>
                <td>{{ $report[$project['id']][$index]['TL'] }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
