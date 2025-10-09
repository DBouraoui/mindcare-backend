<?php

namespace App\DTO\Chat;

use App\Interface\DtoInterface;

class CreateMessageDto implements DtoInterface
{
    public string $conversationId;
    public string $text;
}
