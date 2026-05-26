<?php

namespace App\Enums;

enum CampsiteType: string
{
    case Varkensveld = 'varkensveld';
    case Paardenveld = 'paardenveld';
    case Konijnenveld = 'konijnenveld';
    case Geitenveld = 'geitenveld';
    case Koeienveld = 'koeienveld';
    case Schapenveld = 'schapenveld';
    case Kippenveld = 'kippenveld';
}
