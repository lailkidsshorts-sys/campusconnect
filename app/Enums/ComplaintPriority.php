<?php

namespace App\Enums;

enum ComplaintPriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function label(): string
    {
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::LOW => 'bg-secondary text-white',
            self::MEDIUM => 'bg-info text-dark',
            self::HIGH => 'bg-danger text-white',
        };
    }
}
