<?php

namespace App\Enums\User;

enum Roles: string
{
    case USER = 'user';
    case ADMIN = 'admin';

    public static function all(): array
    {
        return array_column(Roles::cases(), 'value');
    }
}
