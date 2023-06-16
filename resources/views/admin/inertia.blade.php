@extends(backpack_view('blank'))

@section('content')
    <h2><span class="text-capitalize">{{ $title ?? 'Inertia Page' }}</span></h2>
    <div id="backpack" data-page="{{json_encode($page)}}">
        @inertia
    </div>
@endsection
