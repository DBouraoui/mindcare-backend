<?php

namespace App\DTO\user;

use App\Interface\DtoInterface;

class UpdateUserProInformationDto implements DtoInterface
{
    public string $description;
    public string $price;
    public string $country;
    public string $diplome;
}
