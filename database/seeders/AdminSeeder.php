<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        User::query()->UpdateOrCreate([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ],
            ['password' => bcrypt('admin'),]
        )->roles()->sync([$adminRole->id]);
    }
}
