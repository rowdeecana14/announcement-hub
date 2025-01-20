<?php

namespace App\Enums\Announcement;

enum Messages: string
{
    case FETCH = 'Announcements retrieved successfully.';
    case CREATED = 'Announcement created successfully.';
    case NOT_FOUND = 'Announcement not found.';
    case FOUND = 'Announcement found successfully.';
    case UPDATED = 'Announcement updated successfully.';
    case UPDATED_DATES = 'Announcement updated dates successfully.';
    case DELETED = 'Announcement deleted successfully.';

    public static function all(): array
    {
        return array_column(Messages::cases(), 'value');
    }
}
