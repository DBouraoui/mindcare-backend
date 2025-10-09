<?php

namespace App\DTO\User;

use App\Interface\DtoInterface;

class UpdateUserPasswordDto implements DtoInterface
{
    public string $password;
}
