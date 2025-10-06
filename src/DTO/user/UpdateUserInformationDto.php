<?php

namespace App\DTO\user;

use App\Interface\DtoInterface;

class UpdateUserInformationDto implements DtoInterface
{
    public string $firstname;
    public string $lastname;
    public string $city;
    public string $phone;
}
