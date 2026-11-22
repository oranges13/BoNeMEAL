<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

class InviteUser extends Command
{
    protected $signature = 'users:invite {email} {--name= : Name for a new user} {--role=moderator : admin or moderator (new users only)}';

    protected $description = 'Create a user if needed and print a set-password link (use this to bootstrap the first admin)';

    public function handle(): int
    {
        $email = strtolower(trim($this->argument('email')));
        $role = Role::tryFrom((string) $this->option('role'));

        if ($role === null) {
            $this->error('Role must be admin or moderator.');

            return self::FAILURE;
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $this->option('name') ?: $email, 'role' => $role, 'active' => true, 'must_set_password' => true, 'password' => null],
        );

        $user->forceFill(['must_set_password' => true])->save();

        $this->info(route('password.reset', ['token' => Password::broker()->createToken($user), 'email' => $user->email]));

        return self::SUCCESS;
    }
}
