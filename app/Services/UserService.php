<?php

namespace App\Services;

use App\Models\Engineer;
use App\Models\Role;
use App\Models\User;
use App\Notifications\SetPasswordNotification;
use Illuminate\Support\Facades\Password;

/**
 * Class UserService.
 */
class UserService
{
    public function createUserFromEngineer(Engineer $engineer, $email, $role = 'team_lead')
    {
        $user = new User();
        $user->name = $engineer->first_name . ' ' . $engineer->last_name;
        $user->email = $email;
        $user->password = '';

        $role = Role::where('name', $role)->first();
        if ($role) {
            $user->assignRole($role);
        }

        $user->save();

        $engineer->user_id = $user->id;
        $engineer->save();

        return $user;
    }
    public function created(User $user)
    {
        $token = Password::broker()->createToken($user);
        $user->notify(new SetPasswordNotification($token));
        return ['success' => true];
    }
}
