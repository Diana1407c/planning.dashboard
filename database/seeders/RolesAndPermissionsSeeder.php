<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin',
            'project_manager',
            'team_lead',
            'accountant'
        ];

        $permissions = [
            'manage dashboard' => ['admin', 'project_manager', 'team_lead', 'accountant'],
            'manage project' => ['admin', 'project_manager'],
            'manage technology' => ['admin', 'project_manager', 'team_lead'],
            'manage engineer' => ['admin', 'project_manager', 'team_lead'],
            'manage team' => ['admin', 'project_manager', 'team_lead'],
            'manage team_lead_planning' => ['admin', 'project_manager', 'team_lead'],
            'manage project_manager_planning' => ['admin', 'project_manager'],
            'manage users' => ['admin', 'project_manager'],
            'manage reports' => ['admin', 'project_manager', 'accountant'],
            'manage levels' => ['admin', 'project_manager'],
        ];

        foreach ($roles as $role) {
            $roleInstance = Role::query()->updateOrCreate([
                'name' => $role],
            ['guard_name' => 'web']
            );

            foreach ($permissions as $permissionName => $allowedRoles) {
                if (in_array($role, $allowedRoles)) {
                    $permission = Permission::firstOrCreate([
                        'name' => $permissionName,
                        'guard_name' => 'web',
                    ]);
                    $roleInstance->givePermissionTo($permission);
                }
            }
        }
    }
}
