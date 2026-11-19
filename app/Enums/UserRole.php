<?php

namespace App\Enums;

enum UserRole: string
{
    case STUDENT = 'student';
    case DEPT_ADMIN = 'dept_admin';
    case SUPER_ADMIN = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT => 'Student',
            self::DEPT_ADMIN => 'Department Administrator',
            self::SUPER_ADMIN => 'Super Administrator',
        };
    }
}
