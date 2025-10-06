<?php

namespace App\EventSubscriber;

use App\DTO\Notification\CreateNotificationDto;
use App\Event\NotificationCreateEvent;
use App\Service\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class NotificationSubscriber implements EventSubscriberInterface
{

    public function __construct(
        private NotificationService $notificationService
    ) {
    }

    public static function getSubscribedEvents(): array
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
        $notification->createdAt = new \DateTimeImmutable();

        $this->notificationService->createNotification($notification);

        return true;
    }
}
