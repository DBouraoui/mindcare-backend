<?php

namespace App\DTO\user;

use App\Interface\DtoInterface;

class UpdateUserPasswordDto implements DtoInterface
{
    public string $password;
}
