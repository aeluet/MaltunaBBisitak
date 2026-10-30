<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Harrera = 'harrera';
    case Irakaslea = 'irakaslea';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administratzailea',
            self::Harrera => 'Harrera',
            self::Irakaslea => 'Irakaslea',
        };
    }
}
