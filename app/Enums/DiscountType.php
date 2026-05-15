<?php

namespace App\Enums;

enum DiscountType: string
{
    case Flat = 'flat';
    case Percent = 'percent';
}
