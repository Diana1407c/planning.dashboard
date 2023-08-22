<table>
    <thead>
    <tr>
        <th rowspan="2">Projects</th>
        @foreach($dates as $date)
            <th colspan="3" style="text-align: center">{{ $date }}</th>
        @endforeach
    </tr>
    <tr>
        @foreach($dates as $date)
            <th>PM</th>
            <th>TL</th>
            <th>TW</th>
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
                <td>{{ $report[$project['id']][$index]['TM'] }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
