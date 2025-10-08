<?php

namespace App\Controller\Notification;

use App\Entity\Notification;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class NotificationGet extends AbstractController
{
    #[Route(path: '/api/notifications', name: 'notification_get', methods: ['GET'])]
    public function __invoke(#[CurrentUser]User $user){
        try {

            $notification = $user->getNotifications()->map(function (Notification $notification) {
            return [
                "id" => $notification->getId(),
                'title'=> $notification->getTitle(),
                "description" => $notification->getDescription(),
                "type" => $notification->getType(),
                "readAt" => $notification->getReadAt(),
                "createdAt" => $notification->getCreatedAt(),
                ];
            });

            return $this->json($notification);
        } catch( \Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
