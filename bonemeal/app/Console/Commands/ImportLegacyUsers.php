<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLegacyUsers extends Command
{
    protected $signature = 'users:import {path : Path to the legacy storage/database.sqlite}';

    protected $description = 'Import users from the legacy SQLite database (no passwords carried over)';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        config(['database.connections.legacy' => ['driver' => 'sqlite', 'database' => $path, 'foreign_key_constraints' => false]]);
        DB::purge('legacy');

        $created = $skipped = $existing = 0;

        foreach (DB::connection('legacy')->table('users')->get() as $row) {
            $role = self::mapRole((int) $row->role);

            if ($role === null) {
                $skipped++;

                continue;
            }

            $email = strtolower(trim($row->email));

            // Safe to re-run: existing accounts are never touched.
            if (User::where('email', $email)->exists()) {
                $existing++;

                continue;
            }

            User::create([
                'name' => $row->name,
                'email' => $email,
                'role' => $role,
                'active' => true,
                'must_set_password' => true,
                'password' => null,
            ]);
            $created++;
        }

        $this->info("Imported {$created}, already present {$existing}, skipped inactive {$skipped}.");

        return self::SUCCESS;
    }

    /** Legacy roles: 0 inactive, 3 moderator, 6 admin, 8 superuser. */
    public static function mapRole(int $legacy): ?Role
    {
        return match (true) {
            $legacy >= 6 => Role::Admin,
            $legacy >= 3 => Role::Moderator,
            default => null,
        };
    }
}
