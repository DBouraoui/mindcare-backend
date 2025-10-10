<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use App\Enum\NotificationType;
use App\Event\NotificationCreateEvent;
use App\Service\UserService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteBooking extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly EventDispatcherInterface $eventDispatcher,
    ){}
    #[Route('/api/delete-booking/{id}', name: 'delete_booking', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke($id, #[CurrentUser]User $user): Response
    {
        try {
           $booking = $this->userService->deleteBooking($id);

            $this->eventDispatcher->dispatch(
                new NotificationCreateEvent(
                    "Rendez-vous annuler",
                    sprintf("Votre rendez vous du %s a bien été annuler", $booking->getStartAt()->format('d-m-Y')),
                    NotificationType::ALERT->value,
                    $user
                )
            );

            return $this->json(['success' => true]);
        } catch(\Throwable $e){
            return $this->json(['success' => false, 'message'=> $e->getMessage()], 500);
        }
    }
}
