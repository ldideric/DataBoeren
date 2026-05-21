<?php

namespace App\Enums;

enum Province: string
{
    case Drenthe = 'drenthe';
    case Flevoland = 'flevoland';
    case Friesland = 'friesland';
    case Gelderland = 'gelderland';
    case Groningen = 'groningen';
    case Limburg = 'limburg';
    case NoordBrabant = 'noord-brabant';
    case NoordHolland = 'noord-holland';
    case Overijssel = 'overijssel';
    case Utrecht = 'utrecht';
    case Zeeland = 'zeeland';
    case ZuidHolland = 'zuid-holland';

    public function label(): string
    {
        return match ($this) {
            self::NoordBrabant => 'Noord-Brabant',
            self::NoordHolland => 'Noord-Holland',
            self::ZuidHolland => 'Zuid-Holland',
            default => ucfirst($this->value),
        };
    }
}
