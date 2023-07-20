{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
@if(backpack_user()->can('manage dashboard'))
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@endif
@if(backpack_user()->can('manage project'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('project') }}"><i class="nav-icon la la-th-list"></i> Projects</a></li>
@endif
@if(backpack_user()->can('manage technology'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('technology') }}"><i class="nav-icon la la-shekel"></i> Technologies</a></li>
@endif
@if(backpack_user()->can('manage engineer'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('engineer') }}"><i class="nav-icon la la-user"></i> Engineers</a></li>
@endif
@if(backpack_user()->can('manage levels'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('level') }}"><i class="nav-icon la la-user-check"></i> <span>Engineers Level</span></a></li>
@endif
@if(backpack_user()->can('manage team'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('team') }}"><i class="nav-icon la la-group"></i> Teams</a></li>
@endif
@if(backpack_user()->can('manage team_lead_planning'))

    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('monthly_team_lead_planning') }}"><i class="nav-icon la la-history"></i> Monthly TL Planning</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('weekly_team_lead_planning') }}"><i class="nav-icon la la-history"></i> Weekly TL Planning</a></li>
@endif
@if(backpack_user()->can('manage project_manager_planning'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('monthly_project_manager_planning') }}"><i class="nav-icon la la-history"></i> Monthly PM Planning</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('weekly_project_manager_planning') }}"><i class="nav-icon la la-history"></i> Weekly PM Planning</a></li>
@endif
@if(backpack_user()->can('manage users'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
@endif
@if(backpack_user()->can('manage reports'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('reports/comparison') }}"><i class="nav-icon la la-files-o"></i> Comparision Report</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('reports/engineers') }}"><i class="nav-icon la la-files-o"></i> Engineers Report</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('reports/teamwork-time') }}"><i class="nav-icon la la-files-o"></i> Teamwork Time</a></li>
@endif
