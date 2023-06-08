@include('backpack::widgets.inc.wrapper_start')
<button class="btn btn-primary" onclick="window.location='{{ url($widget['route']) }}'">{{ $widget['label'] }}</button>
@include('backpack::widgets.inc.wrapper_end')
