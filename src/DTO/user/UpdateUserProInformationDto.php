<?php

namespace App\DTO\user;

use App\Interface\DtoInterface;

class UpdateUserProInformationDto implements DtoInterface
{
    public string $country;
    public string $diplome;
    public string $description;
    public string $price;
    public string $phone;
    public string $address;
    public string $city;
    public string $siren;
    public string $siret;
    public string $title;
    public string $email;
}
