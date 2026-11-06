<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class BandbSmoke extends Command
{
    protected $signature = 'bandb:smoke';

    protected $description = 'Read-only check that the Ban-Management database is reachable (counts bm_players).';

    public function handle(): int
    {
        try {
            $count = DB::connection('bandb')->table('bm_players')->count();
        } catch (Throwable $e) {
            $this->error('bandb unreachable: '.$e->getMessage());

            return self::FAILURE;
        }

        $this->info("bandb OK: bm_players has {$count} rows.");

        return self::SUCCESS;
    }
}
