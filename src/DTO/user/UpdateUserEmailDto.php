<?php

namespace App\DTO\user;

use App\Interface\DtoInterface;

class UpdateUserEmailDto implements DtoInterface
{
    public string $email;
}
