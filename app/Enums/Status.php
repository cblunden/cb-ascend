<?php

namespace App\Enums;

enum Status: int
{
    case AVAILABLE   = 1;
    case RESERVED    = 2;
    case BORROWED    = 3;
    case UNKNOWN     = 999;
}
