@if (config('backpack.base.show_powered_by') || config('backpack.base.developer_link'))
    <div class="text-muted ml-auto mr-auto">
        @if (config('backpack.base.developer_link') && config('backpack.base.developer_name'))

        @endif
        @if (config('backpack.base.show_powered_by'))

        @endif
    </div>
@endif
