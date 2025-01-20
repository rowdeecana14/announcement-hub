<?php

namespace App\Enums\Auth;

enum Messages: string
{
    case LOGIN_SUCCESS = 'Login successfully.';
    case TOKEN_GENERATED = 'Token generated successfully.';
    case INVALID_CREDENTIALS = 'Invalid credentials.';
    case PASSWORD_UPDATED = 'Password updated successfully.';
    case LOGOUT_SUCCESS = 'Logged out successfully.';

    public static function all(): array
    {
        return array_column(Messages::cases(), 'value');
    }
}
