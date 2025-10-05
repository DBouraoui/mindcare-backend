<?php

namespace App\Service;

use App\DTO\CreateNotificationDto;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function createNotification(CreateNotificationDto $createNotificationDto): Notification {
        $notification = (new Notification())
            ->setUtilisateur($createNotificationDto->user)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setType($createNotificationDto->type)
            ->setDescription($createNotificationDto->description);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}
