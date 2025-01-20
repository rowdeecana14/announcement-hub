<?php

namespace App\Enums\User;

enum Statuses: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case DISAPPROVED = 'disapproved';
    case DEACTIVATED = 'deactivated';

    public static function all(): array
    {
        return array_column(Statuses::cases(), 'value');
    }
}
