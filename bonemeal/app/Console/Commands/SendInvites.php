<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\SetPasswordInvite;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

class SendInvites extends Command
{
    protected $signature = 'users:send-invites
        {emails?* : Only these users}
        {--all : Every active user who still has to set a password}
        {--dry-run : List who would be emailed without sending}
        {--force : Skip the confirmation prompt}';

    protected $description = 'Email set-password links to users who have none yet. Never runs automatically (e.g. not after users:import).';

    public function handle(): int
    {
        $emails = array_map('strtolower', $this->argument('emails'));

        if ($emails === [] && ! $this->option('all')) {
            $this->error('Name the users to email, or pass --all.');

            return self::FAILURE;
        }

        $users = User::where('active', true)->where('must_set_password', true)
            ->when($emails !== [], fn ($q) => $q->whereIn('email', $emails))
            ->orderBy('email')->get();

        if ($users->isEmpty()) {
            $this->info('No matching users are waiting to set a password.');

            return self::SUCCESS;
        }

        $this->table(['Email', 'Name', 'Role'], $users->map(fn (User $u) => [$u->email, $u->name, $u->role->value])->all());

        if ($this->option('dry-run')) {
            $this->info("Dry run: {$users->count()} would be emailed.");

            return self::SUCCESS;
        }

        if (! $this->option('force') && ! $this->confirm("Email {$users->count()} user(s)?")) {
            $this->info('Nothing sent.');

            return self::SUCCESS;
        }

        $failed = 0;
        foreach ($users as $user) {
            try {
                $url = route('password.reset', ['token' => Password::broker()->createToken($user), 'email' => $user->email]);
                $user->notify(new SetPasswordInvite($url));
            } catch (\Throwable $e) {
                $failed++;
                $this->error("{$user->email}: {$e->getMessage()}");
            }
        }

        $this->info('Sent '.($users->count() - $failed).", failed {$failed}.");

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
