<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use App\Notifications\SetPasswordInvite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use PDO;
use Tests\TestCase;

class ImportLegacyUsersTest extends TestCase
{
    use RefreshDatabase;

    private string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = tempnam(sys_get_temp_dir(), 'legacy').'.sqlite';
        $pdo = new PDO('sqlite:'.$this->path);
        $pdo->exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT, password TEXT, role INTEGER DEFAULT 0)');
        $insert = $pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
        foreach ([['Super', 'Super@Example.com', 8], ['Admin', 'admin@example.com', 6], ['Mod', 'mod@example.com', 3], ['Pending', 'pending@example.com', 0]] as [$n, $e, $r]) {
            $insert->execute([$n, $e, bcrypt('legacy-secret'), $r]);
        }
    }

    protected function tearDown(): void
    {
        @unlink($this->path);
        parent::tearDown();
    }

    public function test_maps_roles_and_skips_inactive_users(): void
    {
        $this->artisan('users:import', ['path' => $this->path])->assertSuccessful();

        $this->assertSame(Role::Admin, User::where('email', 'super@example.com')->first()->role);
        $this->assertSame(Role::Admin, User::where('email', 'admin@example.com')->first()->role);
        $this->assertSame(Role::Moderator, User::where('email', 'mod@example.com')->first()->role);
        $this->assertDatabaseMissing('users', ['email' => 'pending@example.com']);
    }

    public function test_imported_users_have_no_password_and_cannot_log_in(): void
    {
        $this->artisan('users:import', ['path' => $this->path])->assertSuccessful();

        $user = User::where('email', 'mod@example.com')->first();
        $this->assertNull($user->password);
        $this->assertTrue($user->must_set_password);

        $this->post('/login', ['email' => 'mod@example.com', 'password' => 'legacy-secret']);
        $this->assertGuest();
    }

    public function test_import_is_safe_to_run_twice_and_keeps_existing_passwords(): void
    {
        $this->artisan('users:import', ['path' => $this->path])->assertSuccessful();
        $user = User::where('email', 'mod@example.com')->first();
        $user->forceFill(['password' => Hash::make('new-password'), 'must_set_password' => false])->save();

        $this->artisan('users:import', ['path' => $this->path])->assertSuccessful();

        $this->assertSame(3, User::count());
        $this->assertFalse($user->fresh()->must_set_password);
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_import_sends_no_email(): void
    {
        Notification::fake();

        $this->artisan('users:import', ['path' => $this->path])->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_send_invites_requires_explicit_scope(): void
    {
        Notification::fake();
        $this->artisan('users:import', ['path' => $this->path])->assertSuccessful();

        $this->artisan('users:send-invites')->assertFailed();
        $this->artisan('users:send-invites', ['--all' => true, '--dry-run' => true])->assertSuccessful();
        Notification::assertNothingSent();
    }

    public function test_send_invites_emails_only_chosen_users(): void
    {
        Notification::fake();
        $this->artisan('users:import', ['path' => $this->path])->assertSuccessful();

        $this->artisan('users:send-invites', ['emails' => ['mod@example.com'], '--force' => true])->assertSuccessful();

        Notification::assertSentTo(User::where('email', 'mod@example.com')->first(), SetPasswordInvite::class);
        Notification::assertNotSentTo(User::where('email', 'admin@example.com')->first(), SetPasswordInvite::class);
    }

    public function test_send_invites_all_skips_users_who_have_set_a_password(): void
    {
        Notification::fake();
        $this->artisan('users:import', ['path' => $this->path])->assertSuccessful();
        User::where('email', 'mod@example.com')->update(['must_set_password' => false]);

        $this->artisan('users:send-invites', ['--all' => true, '--force' => true])->assertSuccessful();

        Notification::assertSentTimes(SetPasswordInvite::class, 2);
    }
}
