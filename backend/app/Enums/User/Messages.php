<?php

namespace App\Enums\User;

enum Messages: string
{
    case USER_INACTIVE = 'User account is inactive.';
    case USER_RESTRICTED = 'User account is restricted.';
    case PROFILE_FETCH = 'Profile fetch successfully.';
    case UPDATED_DETAILS = 'User details updated successfully.';
    case EMAIL_TAKEN = 'The email has already been taken.';

    public static function all(): array
    {
        return array_column(Messages::cases(), 'value');
    }
}
