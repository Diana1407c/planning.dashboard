<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

class AddPermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:add-permission {permission : The short permission name} {--roles : Comma separated roles}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permission';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $permissionName = $this->argument('permission');
        $roleNames = $this->argument('roles');

        $roles = Role::query()->whereIn('name', explode(',', $roleNames))->get();

        if (!$roles) {
            return;
        }

        $permission = Permission::firstOrCreate([
            'name' => "manage $permissionName",
            'guard_name' => 'web',
        ]);

        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
        }
    }
}
