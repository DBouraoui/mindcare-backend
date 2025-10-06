<?php

namespace App\Service;

use App\DTO\Notification\CreateNotificationDto;
use App\DTO\Notification\UpdateNotificationDto;
use App\Entity\Notification;
use App\Interface\DtoInterface;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationRepository $notificationRepository,
    ) {}

    public function createNotification(CreateNotificationDto $createNotificationDto): Notification {

        $notification = (new Notification())
            ->setTitle($createNotificationDto->title)
            ->setDescription($createNotificationDto->description)
            ->setType($createNotificationDto->type)
            ->setUtilisateur($createNotificationDto->user)
            ->setCreatedAt($createNotificationDto->createdAt);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        return $notification;
    }

    public function updateNotification(DtoInterface $dto): Notification {
        $notification = $this->notificationRepository->find($dto->id);

        if (!$notification) {
            throw new \Exception("Notification not found");
        }

        $notification->setReadAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $notification;
    }
}
