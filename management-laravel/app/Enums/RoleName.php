<?php

declare(strict_types=1);

namespace App\Enums;

enum RoleName: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Employee = 'employee';

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
