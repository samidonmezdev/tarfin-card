<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case DUE = 'DUE';
    case REPAID = 'REPAID';
    case PARTIAL = 'PARTIAL';
}
