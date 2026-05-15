<?php

namespace App\Enums;

enum CampsiteType: string
{
    case Tent = 'tent';
    case Trekkerveld = 'trekkerveld';
    case CamperVan = 'camper_van';
    case Caravan = 'caravan';
}
