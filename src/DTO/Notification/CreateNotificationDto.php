<?php

namespace App\DTO\Notification;

use App\Entity\User;
use App\Interface\DtoInterface;

class CreateNotificationDto implements DtoInterface
{
    public string $title;
    public string $description;
    public string $type;
    public User $user;
    public \DateTimeImmutable $createdAt;
}
