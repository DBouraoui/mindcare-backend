<?php

namespace App\DTO\Contact;

use App\Interface\DtoInterface;

class CreateContactDto implements DtoInterface
{
    public string $email;
    public string $title;
    public string $type;
    public string $message;

}
