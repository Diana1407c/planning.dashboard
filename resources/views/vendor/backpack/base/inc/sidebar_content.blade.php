{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<?php if(backpack_user()->hasRole('admin|project_manager')):?>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('project') }}"><i class="nav-icon la la-th-list"></i> Projects</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('technology') }}"><i class="nav-icon la la-shekel"></i> Technologies</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('engineer') }}"><i class="nav-icon la la-user"></i> Engineers</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('team') }}"><i class="nav-icon la la-group"></i> Teams</a></li>
<?php endif; ?>
<?php if(backpack_user()->hasRole('team_lead|admin|project_manager')):?>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('team_lead_planning') }}"><i class="nav-icon la la-history"></i> TL Planning</a></li>
<?php endif; ?>
<?php if(backpack_user()->hasRole('admin|project_manager')):?>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('project_manager_planning') }}"><i class="nav-icon la la-history"></i> PM Planning</a></li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> Authentication</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>Users</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('role') }}"><i class="nav-icon la la-group"></i> <span>Roles</span></a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('permission') }}"><i class="nav-icon la la-key"></i> <span>Permissions</span></a></li>
    </ul>
</li>
<?php endif; ?>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('reports/comparison') }}"><i class="nav-icon la la-files-o"></i> Comparision Report</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('reports/engineers') }}"><i class="nav-icon la la-files-o"></i> Engineers Report</a></li>
