<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Moderator = 'moderator';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
