<?php

namespace App;

enum UserRole: string
{
    case ADMIN = 'admin';
    case KASIR = 'kasir';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Asministrator',
            self::KASIR => 'Kasir',
        };
    }
}
