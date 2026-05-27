<?php

namespace App\Enums;

enum StockType: string
{
    case Rental = 'rental';
    case Consumable = 'consumable';
}
