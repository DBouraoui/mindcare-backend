<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class NotificationCreateEvent extends Event
{
    public string $title;
    public string $description;
    public string $type;
    public User $user;

    public function __construct(
        string $title,
        string $description,
        string $type,
        User $user
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
        $this->user = $user;
    }
}
