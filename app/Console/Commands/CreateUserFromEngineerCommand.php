<?php

namespace App\Console\Commands;

use App\Models\Engineer;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Console\Command;

class CreateUserFromEngineerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:create-user-from-engineer {email : The email of the engineer} {--role=team_lead : The role to assign to the user (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a user from an engineer based on email';

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
     */
    public function handle(UserService $userService)
    {
        $email = $this->argument('email');
        $role = $this->option('role');

        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $this->info('An user with this email already exists.');
            return;
        }

        $engineer = Engineer::where('email', $email)->first();
        if (!$engineer) {
            $this->info('There is no engineer with this email.');
            return;
        }

        $user = $userService->createUserFromEngineer($engineer, $email, $role);
        if (!$user) {
            $this->info('Failed to create the user.');
            return;
        }

        $result = $userService->created($user);

        if ($result['success']) {
            $this->info('The user was created successfully and received a password reset notification.');
        } else {
            $this->info('User unreceived a password reset notification.');
        }
    }
}
