<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Str;

enum CampsiteType: string implements HasColor, HasLabel
{
    case Paardenveld = 'paardenveld';
    case Varkensveld = 'varkensveld';
    case Konijnenveld = 'konijnenveld';
    case Geitenveld = 'geitenveld';
    case Koeienveld = 'koeienveld';
    case Schapenveld = 'schapenveld';
    case Kippenveld = 'kippenveld';

    public function getLabel(): ?string
    {
        return $this->getHeadline();
    }

    public function getColor(): string|array|null
    {
        return 'gray';
    }

    public function getHeadline(): string
    {
        return Str::headline($this->value);
    }

    public function getDescription(): string
    {
        return match ($this) {
            self::Paardenveld => 'Camperveld',
            self::Varkensveld => 'Trekkersveld',
            default => 'Tentenveld',
        };
    }
}
