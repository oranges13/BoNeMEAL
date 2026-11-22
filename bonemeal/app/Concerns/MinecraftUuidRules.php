<?php

namespace App\Concerns;

use App\Models\User;
use App\Support\MinecraftUuid;
use Closure;
use Illuminate\Validation\Rule;

trait MinecraftUuidRules
{
    /**
     * Rules for an optional linked Minecraft UUID. Call normalizeUuid() on the input first.
     *
     * @return array<int, mixed>
     */
    protected function uuidRules(?int $userId = null): array
    {
        return [
            'nullable',
            'string',
            function (string $attribute, mixed $value, Closure $fail) {
                if ($value !== null && MinecraftUuid::normalize($value) === null) {
                    $fail('The Minecraft UUID is not valid.');
                }
            },
            Rule::unique(User::class, 'minecraft_uuid')->ignore($userId),
        ];
    }
}
