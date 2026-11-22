<?php

namespace App\Http\Requests\Settings;

use App\Concerns\MinecraftUuidRules;
use App\Concerns\ProfileValidationRules;
use App\Support\MinecraftUuid;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    use MinecraftUuidRules, ProfileValidationRules;

    protected function prepareForValidation(): void
    {
        $uuid = trim((string) $this->input('minecraft_uuid'));

        $this->merge(['minecraft_uuid' => $uuid === '' ? null : (MinecraftUuid::normalize($uuid) ?? $uuid)]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...$this->profileRules($this->user()->id),
            'minecraft_uuid' => $this->uuidRules($this->user()->id),
        ];
    }
}
