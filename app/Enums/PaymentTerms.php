<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentTerms: int
{
    case MONTH6 = 6;
    case MONTH3 = 3;
}
