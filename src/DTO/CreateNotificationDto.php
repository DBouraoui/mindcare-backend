<?php

namespace App\DTO;

use App\Entity\User;
use App\Interface\DtoInterface;

class CreateNotificationDto implements DtoInterface
{
    public string $title;
    public string $description;
    public User $user;
    public string $type;
}
