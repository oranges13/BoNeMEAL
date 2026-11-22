<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\MinecraftUuidRules;
use App\Concerns\ProfileValidationRules;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SetPasswordInvite;
use App\Support\MinecraftUuid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class UserController extends Controller
{
    use MinecraftUuidRules, ProfileValidationRules;

    public function index(Request $request): Response
    {
        return Inertia::render('admin/Users', [
            'users' => User::orderBy('name')->get(['id', 'name', 'email', 'role', 'active', 'must_set_password', 'minecraft_uuid']),
            'roles' => array_map(fn (Role $role) => $role->value, Role::cases()),
            'inviteUrl' => $request->session()->get('inviteUrl'),
            'inviteEmailed' => (bool) $request->session()->get('inviteEmailed'),
        ]);
    }

    /** Create a user with no password; an invite link lets them set one. */
    public function store(Request $request): RedirectResponse
    {
        $this->normalizeUuid($request);
        $request->merge(['email' => strtolower(trim((string) $request->input('email')))]);

        $data = $request->validate([
            ...$this->profileRules(),
            'role' => ['required', Rule::enum(Role::class)],
            'minecraft_uuid' => $this->uuidRules(),
        ]);

        $user = User::create([...$data, 'active' => true, 'must_set_password' => true, 'password' => null]);

        $url = $this->inviteUrl($user);

        // Email the invite; the on-screen link stays as a fallback if mail is down.
        try {
            $user->notify(new SetPasswordInvite($url));
            $emailed = true;
        } catch (Throwable $e) {
            report($e);
            $emailed = false;
        }

        return to_route('admin.users.index')
            ->with('inviteUrl', $url)
            ->with('inviteEmailed', $emailed);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->normalizeUuid($request);

        $data = $request->validate([
            'name' => $this->nameRules(),
            'role' => ['required', Rule::enum(Role::class)],
            'active' => ['required', 'boolean'],
            'minecraft_uuid' => $this->uuidRules($user->id),
        ]);

        // Never let the last active admin be demoted or deactivated (including yourself).
        $losesAdmin = $user->isAdmin() && ($data['role'] !== Role::Admin->value || ! $data['active']);
        if ($losesAdmin && ! User::where('role', Role::Admin)->where('active', true)->whereKeyNot($user->id)->exists()) {
            return back()->withErrors(['role' => 'At least one active admin is required.']);
        }

        $user->update($data);

        return back();
    }

    /** Issue a fresh set-password link (invite or admin-initiated reset) without needing mail. */
    public function invite(User $user): RedirectResponse
    {
        $user->forceFill(['must_set_password' => true])->save();

        return $this->inviteRedirect($user);
    }

    private function inviteRedirect(User $user): RedirectResponse
    {
        return to_route('admin.users.index')->with('inviteUrl', $this->inviteUrl($user));
    }

    private function inviteUrl(User $user): string
    {
        return route('password.reset', ['token' => Password::broker()->createToken($user), 'email' => $user->email]);
    }

    private function normalizeUuid(Request $request): void
    {
        $uuid = trim((string) $request->input('minecraft_uuid'));

        $request->merge(['minecraft_uuid' => $uuid === '' ? null : (MinecraftUuid::normalize($uuid) ?? $uuid)]);
    }
}
