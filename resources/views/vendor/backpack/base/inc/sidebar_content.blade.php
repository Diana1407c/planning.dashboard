{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('project') }}"><i class="nav-icon la la-th-list"></i> Projects</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('technology') }}"><i class="nav-icon la la-shekel"></i> Technologies</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('engineer') }}"><i class="nav-icon la la-user"></i> Engineers</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('team') }}"><i class="nav-icon la la-group"></i> Teams</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('team_lead_planning') }}"><i class="nav-icon la la-history"></i> TL Planning</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('project_manager_planning') }}"><i class="nav-icon la la-history"></i> PM Planning</a></li>
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
        <i class="nav-icon la la-files-o"></i> Reports
    </a>
    <ul class="dropdown-menu">
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('reports/comparison') }}">
                <i class="la la-files-o"></i> Comparision Report
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('reports/engineers') }}">
                <i class="la la-files-o"></i> Engineers Report
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ backpack_url('technology') }}">
                <i class="la la-files-o"></i> Third Type
            </a>
        </li>
    </ul>
</li>
