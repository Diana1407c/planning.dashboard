<?php
function countStackEngineers($array, $index): int
{
    $sum = 0;
    if (isset($array[$index])) {
        foreach ($array[$index] as $subarray) {
            $sum += count($subarray);
        }
    }
    return $sum + 2 + count($array[$index])*2;
}

?>

<table>
    <thead>
    <tr>
        <th>Stack</th>
        <th>Technology</th>
        <th>Engineer</th>
        @foreach($dates as $date)
            <th>{{ $date['formatted'] }}</th>
        @endforeach
        <th>Totals</th>
    </tr>
    </thead>
    <tbody>
    @foreach($headingGroup as $stackID => $stackGroup)
        <tr>
            <td rowspan="<?php echo countStackEngineers($headingGroup, $stackID) ?>">{{ $stacks[$stackID] }}</td>
        </tr>
        @foreach($stackGroup as $techID => $techGroup)
            <tr>
                <td rowspan="<?php echo count($techGroup)+2 ?>">{{ $technologies[$techID] }}</td>
            </tr>
            @foreach($techGroup as $engineerID => $engineerName)
                <tr>
                    <td>{{ $engineerName }}</td>
                    @foreach($dates as $date)
                        <td>{{ $logging[$date['index']][$stackID][$techID][$engineerID] ?? 0 }}</td>
                    @endforeach
                    <td>{{ number_format($grandTotals['engineers'][$engineerID], 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="background: #dbd4ad;">{{ $technologies[$techID] }} Total</td>
                @foreach($dates as $date)
                    <td style="background: #dbd4ad;">{{ $logging[$date['index']][$stackID][$techID]['total_tech'] ?? 0 }}</td>
                @endforeach
                <td style="background: #dbd4ad;">{{ number_format($grandTotals['tech'][$techID], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <td style="background: #beedb2;" colspan="2">{{ $stacks[$stackID] }} Total</td>
            @foreach($dates as $date)
                <td style="background: #beedb2;">{{ $logging[$date['index']][$stackID]['total_stack'] ?? 0 }}</td>
            @endforeach
            <td style="background: #beedb2;">{{ number_format($grandTotals['stack'][$stackID], 2) }}</td>
        </tr>
    @endforeach
    <tr>
        <td style="background: #84a0ad;" colspan="3">Month Total</td>
        @foreach($dates as $date)
            <td style="background: #84a0ad;">{{ $logging[$date['index']]['total_month'] ?? 0 }}</td>
        @endforeach
        <td style="background: #84a0ad;">{{ number_format($grandTotals['total'], 2) }}</td>
    </tr>
    </tbody>
</table>
