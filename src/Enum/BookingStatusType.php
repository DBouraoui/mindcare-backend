<?php

namespace App\Enum;

enum BookingStatusType: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case DONE = 'done';
}
