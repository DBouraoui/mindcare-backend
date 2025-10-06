<?php

namespace App\Controller\Notification;
use App\DTO\Notification\CreateNotificationDto;
use App\DTO\Notification\UpdateNotificationDto;
use App\Entity\User;
use App\Service\NotificationService;
use App\Service\UtilitaireService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class NotificationUpdate extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilService,
        private readonly NotificationService $notificationService,
        private readonly LoggerInterface $logger,
    ){}
    #[Route(path: '/api/notification', name: 'notification_update', methods: ['PUT'])]
    public function __invoke(#[CurrentUser]User $user, Request $request){
        try {
            $request = json_decode($request->getContent());

           $notificationDto = $this->utilService->mapAndValidateRequestDto(
                $request,
                new UpdateNotificationDto()
            );

          $notification = $this->notificationService->updateNotification($notificationDto);

          $this->logger->info(sprintf("Notification updated with id: %s for user %s", $notification->getId(), $user->getEmail()));

          return $this->json(['success' => true],201);
        }catch(\Throwable $e) {
            return $this->json(
                $e->getMessage(),
                500
            );
        }
    }
}
