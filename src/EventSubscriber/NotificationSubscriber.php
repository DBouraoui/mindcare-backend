<?php

namespace App\EventSubscriber;

use App\DTO\CreateNotificationDto;
use App\Event\NotificationCreateEvent;
use App\Service\NotificationService;
use Doctrine\Common\EventSubscriber;

class NotificationSubscriber implements EventSubscriber
{

    public function __construct(
        private readonly NotificationService $notificationService
    ) {
    }

    public function getSubscribedEvents()
    {
        return [
            NotificationCreateEvent::class => 'onCreateNotification',
        ];
    }

    public function onCreateNotification(NotificationCreateEvent $event):bool
    {
        $notification = new CreateNotificationDto();
        $notification->title = $event->title;
        $notification->user = $event->user;
        $notification->description = $event->description;
        $notification->type = $event->type;

        $this->notificationService->createNotification($notification);

        return true;
    }
}
