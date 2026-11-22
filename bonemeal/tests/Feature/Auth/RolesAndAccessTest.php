<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\SetPasswordInvite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class RolesAndAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', ['name' => 'X', 'email' => 'x@example.com', 'password' => 'password', 'password_confirmation' => 'password'])->assertNotFound();
    }

    public function test_deactivated_user_cannot_log_in(): void
    {
        $user = User::factory()->inactive()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $this->assertGuest();
    }

    public function test_user_without_password_cannot_log_in_until_they_set_one(): void
    {
        $user = User::factory()->needsPassword()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $this->assertGuest();

        $token = Password::broker()->createToken($user);
        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'a-brand-new-password',
            'password_confirmation' => 'a-brand-new-password',
        ])->assertSessionHasNoErrors();

        $this->assertFalse($user->fresh()->must_set_password);

        $this->post('/login', ['email' => $user->email, 'password' => 'a-brand-new-password']);
        $this->assertAuthenticatedAs($user);
    }

    public function test_moderator_is_denied_user_management_and_admin_is_allowed(): void
    {
        $this->actingAs(User::factory()->create())->get('/admin/users')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get('/admin/users')->assertOk();
    }

    public function test_moderator_cannot_create_users(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/users', ['name' => 'N', 'email' => 'n@example.com', 'role' => 'admin'])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'n@example.com']);
    }

    public function test_admin_creates_user_without_password_and_gets_invite_link(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/users', ['name' => 'New Mod', 'email' => 'NEW@example.com', 'role' => 'moderator'])
            ->assertRedirect('/admin/users')
            ->assertSessionHas('inviteUrl');

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertNull($user->password);
        $this->assertTrue($user->must_set_password);
    }

    public function test_creating_a_user_emails_the_invite(): void
    {
        Notification::fake();

        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/users', ['name' => 'New Mod', 'email' => 'new@example.com', 'role' => 'moderator'])
            ->assertSessionHas('inviteEmailed', true);

        Notification::assertSentTo(User::where('email', 'new@example.com')->first(), SetPasswordInvite::class);
    }

    public function test_admin_can_deactivate_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $mod = User::factory()->create();

        $this->actingAs($admin)
            ->patch("/admin/users/{$mod->id}", ['name' => $mod->name, 'role' => 'moderator', 'active' => false])
            ->assertSessionHasNoErrors();

        $this->assertFalse($mod->fresh()->active);
    }

    public function test_last_active_admin_cannot_be_demoted_or_deactivated(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch("/admin/users/{$admin->id}", ['name' => $admin->name, 'role' => 'moderator', 'active' => true])
            ->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->isAdmin());
    }

    public function test_user_can_link_and_unlink_their_own_minecraft_uuid(): void
    {
        $user = User::factory()->create();
        $uuid = '069A79F4-44E9-4726-A5BE-FCA90E38AAF5';

        $this->actingAs($user)->patch('/settings/profile', ['name' => $user->name, 'email' => $user->email, 'minecraft_uuid' => $uuid])
            ->assertSessionHasNoErrors();
        $this->assertSame(strtolower($uuid), $user->fresh()->minecraft_uuid);

        $this->actingAs($user)->patch('/settings/profile', ['name' => $user->name, 'email' => $user->email, 'minecraft_uuid' => ''])
            ->assertSessionHasNoErrors();
        $this->assertNull($user->fresh()->minecraft_uuid);
    }

    public function test_a_player_already_linked_to_another_user_is_refused(): void
    {
        $uuid = '069a79f4-44e9-4726-a5be-fca90e38aaf5';
        User::factory()->create(['minecraft_uuid' => $uuid]);
        $other = User::factory()->create();

        $this->actingAs($other)->patch('/settings/profile', ['name' => $other->name, 'email' => $other->email, 'minecraft_uuid' => $uuid])
            ->assertSessionHasErrors('minecraft_uuid');
        $this->assertNull($other->fresh()->minecraft_uuid);
    }

    public function test_admin_can_link_anyones_uuid_and_invalid_uuid_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $mod = User::factory()->create();
        $uuid = '069a79f4-44e9-4726-a5be-fca90e38aaf5';

        $this->actingAs($admin)->patch("/admin/users/{$mod->id}", ['name' => $mod->name, 'role' => 'moderator', 'active' => true, 'minecraft_uuid' => $uuid])
            ->assertSessionHasNoErrors();
        $this->assertSame($uuid, $mod->fresh()->minecraft_uuid);

        $this->actingAs($admin)->patch("/admin/users/{$mod->id}", ['name' => $mod->name, 'role' => 'moderator', 'active' => true, 'minecraft_uuid' => 'not-a-uuid'])
            ->assertSessionHasErrors('minecraft_uuid');
    }

    public function test_invite_command_bootstraps_an_admin_who_can_set_a_password(): void
    {
        $this->artisan('users:invite', ['email' => 'Boss@example.com', '--name' => 'Boss', '--role' => 'admin'])->assertSuccessful();

        $user = User::where('email', 'boss@example.com')->firstOrFail();
        $this->assertTrue($user->isAdmin());
        $this->assertNull($user->password);
        $this->assertTrue($user->must_set_password);
    }
}
