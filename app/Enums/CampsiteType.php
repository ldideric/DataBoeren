<?php

namespace App\Enums;

use \Illuminate\Support\Str;

enum CampsiteType: string
{
    case Paardenveld = 'paardenveld';
    case Varkensveld = 'varkensveld';
    case Konijnenveld = 'konijnenveld';
    case Geitenveld = 'geitenveld';
    case Koeienveld = 'koeienveld';
    case Schapenveld = 'schapenveld';
    case Kippenveld = 'kippenveld';

    public function getHeadline(): string
    {
        return Str::headline($this->value);
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Paardenveld => 'Camperveld',
            self::Varkensveld => 'Trekkersveld',
            self::Konijnenveld => 'Tentenveld',
            self::Geitenveld => 'Tentenveld',
            self::Koeienveld => 'Tentenveld',
            self::Schapenveld => 'Tentenveld',
            self::Kippenveld => 'Tentenveld',
        };
    }
}
