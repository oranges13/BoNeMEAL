<?php

namespace Tests\Feature;

use Tests\TestCase;

class BandbConnectionTest extends TestCase
{
    public function test_bandb_is_configured_separately_from_the_default(): void
    {
        $this->assertNotSame('bandb', config('database.default'));
        $this->assertIsArray(config('database.connections.bandb'));
        $this->assertSame('mysql', config('database.connections.bandb.driver'));
        $this->assertNotSame(
            config('database.connections.mysql'),
            config('database.connections.bandb'),
        );
    }

    public function test_bandb_has_no_migration_path(): void
    {
        $this->assertArrayNotHasKey('migrations', config('database.connections.bandb'));
        $this->assertArrayNotHasKey('migrations_path', config('database.connections.bandb'));
    }

    public function test_no_migration_targets_bandb(): void
    {
        $files = glob(database_path('migrations/*.php')) ?: [];

        foreach ($files as $file) {
            $this->assertStringNotContainsString(
                'bandb',
                (string) file_get_contents($file),
                basename($file).' must not touch the plugin database',
            );
        }
    }

    public function test_no_migration_uses_the_bandb_connection_via_property(): void
    {
        foreach (glob(database_path('migrations/*.php')) ?: [] as $file) {
            $this->assertDoesNotMatchRegularExpression(
                '/\$connection\s*=\s*[\'"]bandb[\'"]/',
                (string) file_get_contents($file),
            );
        }
    }
}
