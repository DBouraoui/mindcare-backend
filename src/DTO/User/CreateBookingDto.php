<?php

namespace App\DTO\User;

use App\Interface\DtoInterface;

class CreateBookingDto implements DtoInterface
{
    public string $proId;
    public string $startAt;
    public string $endAt;
    public string $note;
}
