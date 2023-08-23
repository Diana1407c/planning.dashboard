<x-mail::message>
# Hello!

<p>You receive this message because a logged time discrepancy report has been made</p>
<p>The required amount of hours till now is {{ $data->requiredHours }} hours</p>
@component('mail::table')
    <table id="logged-time">
        <thead>
        <tr>
            <th>Team</th>
            <th class="names">Engineer</th>
            @foreach ($data->dates as $date)
                <th class="hours">{{ $date->format('l') }}</th>
            @endforeach
            <th class="hours">Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data->engineers as $engineer)
            <tr>
                <td>{{ $engineer->team ? $engineer->team->name : '' }}</td>
                <td class="names">{{ $engineer->first_name.' '.$engineer->last_name }}</td>
                @foreach ($data->dates as $date)
                    <td class="hours">
                        @if(isset($data->hours[$engineer->id]))
                            {{ $data->hours[$engineer->id]->where('date', $date->format('Y-m-d'))->sum('hours') }}
                        @else
                            0
                        @endif
                    </td>
                @endforeach
                <td class="danger-total hours">{{ !empty($data->hours[$engineer->id]) ? $data->hours[$engineer->id]->sum('hours') : 0 }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endcomponent
</x-mail::message>

<style>
    .names{
        width: 250px;
    }

    .hours{
        width: 50px;
        text-align: center;
    }

    .danger-total{
        background-color: #DC3545;
        color: white !important;
    }

    #logged-time {
        border-collapse: collapse;
        border: 1px solid #718096;
    }

    #logged-time td, #logged-time th{
        border: 1px solid #718096;
        padding: 8px 12px;
    }

    #logged-time thead{
        background-color: greenyellow;
    }
</style>
