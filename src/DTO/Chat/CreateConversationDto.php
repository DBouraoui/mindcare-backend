<?php

namespace App\DTO\Chat;

use App\Interface\DtoInterface;

class CreateConversationDto implements  DtoInterface
{
    public string $user2;
    public string $text;
}
