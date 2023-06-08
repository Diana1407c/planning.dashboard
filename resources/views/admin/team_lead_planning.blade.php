@extends(backpack_view('blank'))

@section('content')
<main class="main pt-2">
    <div class="container-fluid">
        <h2><span class="text-capitalize">Team Lead Planning</span></h2>
    </div>
    <div class="container-fluid animated fadeIn" id="backpack" data-page="{{json_encode($page)}}">
        @inertia
    </div>
</main>
@endsection
