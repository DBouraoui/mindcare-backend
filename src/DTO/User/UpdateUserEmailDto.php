<?php

namespace App\DTO\User;

use App\Interface\DtoInterface;

class UpdateUserEmailDto implements DtoInterface
{
    public string $email;
}
