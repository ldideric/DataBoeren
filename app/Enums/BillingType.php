<?php

namespace App\Enums;

enum BillingType: string
{
    case OneTime = 'one_time';
    case PerNight = 'per_night';
    case Rental = 'rental';
}
