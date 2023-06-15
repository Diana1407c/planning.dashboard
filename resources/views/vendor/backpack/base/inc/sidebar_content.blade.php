{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('project') }}"><i class="nav-icon la la-th-list"></i> Projects</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('technology') }}"><i class="nav-icon la la-shekel"></i> Technologies</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('engineer') }}"><i class="nav-icon la la-user"></i> Engineers</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('team') }}"><i class="nav-icon la la-group"></i> Teams</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('team_lead_planning') }}"><i class="nav-icon la la-history"></i> TL Planning</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('project_manager_planning') }}"><i class="nav-icon la la-history"></i> PM Planning</a></li>
<li class="nav-item nav-dropdown open">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Authentication</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('users') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="nav-icon la la-group"></i> <span>Draft:Roles</span></a></li>
        <li class="nav-item"><a class="nav-link" href="#"><i class="nav-icon la la-key"></i> <span>Draft:Permissions</span></a></li>
    </ul>
</li>
