<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\DTO\User\CreateBookingDto;
use App\Entity\User;
use App\Enum\NotificationType;
use App\Event\NotificationCreateEvent;
use App\Service\UserService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CreateBooking extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly UtilitaireService $utilitaireService,
        private readonly EventDispatcherInterface $eventDispatcher
    ){}

    #[Route('/api/create-booking', name: 'api_create_booking', methods: ['POST'])]
    public function __invoke(#[CurrentUser]User $user, Request $request): Response
    {
        try {
            $data = json_decode($request->getContent());

           $bookingDto = $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new CreateBookingDto()
            );

            $booking = $this->userService->createBooking($bookingDto, $user);

            $this->eventDispatcher->dispatch(
                new NotificationCreateEvent(
                    "Nouveau rendez-vous",
                    sprintf("Vous avez réserver un nouveau rendez-vous le %s de %s à %s avec le docteur %s",
                        $booking->getCreatedAt()->format('d-m-y'),
                    $booking->getStartAt()->format('H:i'),
                    $booking->getEndAt()->format('H:i'),
                    $booking->getPro()->getUtilisateur()->getLastname()
                    ),
                    NotificationType::SIMPLE->value,
                    $user
                )
            );

            $this->eventDispatcher->dispatch(
                new NotificationCreateEvent(
                    "Nouveau rendez-vous",
                    sprintf("Un nouveau rendez vous a été programmer le %s avec le patient %s",
                    $booking->getStartAt()->format('d-m-y'),
                    $booking->getUtilisateur()->getLastname()
                    ),
                    NotificationType::SIMPLE->value,
                    $booking->getPro()->getUtilisateur()
                )
            );

            //Todo send email

            return $this->json(['success' => true],201);
        } catch(\Exception $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
