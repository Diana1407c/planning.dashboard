<div class="col-md-8">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Engineer History</h3>
        </div>
        <div class="scroll-container">
            @if ($histories->count() > 0)
                <ul>
                    @foreach ($histories as $history)
                        <li>
                            <i>{{ $history->label }}</i>
                            <strong>{{ $history->value }}</strong>
                            <small>{{ $history->created_at }}</small>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No history available for this engineer.</p>
            @endif
        </div>
    </div>
</div>

@push('after_styles')
    <style>
        .scroll-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #e3e3e3;
            padding: 15px;
        }
    </style>
@endpush
