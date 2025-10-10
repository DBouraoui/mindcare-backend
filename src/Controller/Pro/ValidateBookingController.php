<?php

declare(strict_types=1);

namespace App\Controller\Pro;

use App\Entity\User;
use App\Enum\NotificationType;
use App\Event\NotificationCreateEvent;
use App\Service\ProService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ValidateBookingController extends AbstractController
{
    public function __construct(
        private readonly ProService $proService,
        private readonly EventDispatcherInterface $eventSubscriber,
    ){}

    #[Route('/api/validate-booking/{id}/{status}', name: 'validate-booking', methods: ['PATCH'])]
    public function __invoke(#[CurrentUser] User $user, string $id, string $status): Response
    {
        try {
            $booking = $this->proService->validateBooking($id, $status);

            [$title, $message] = match ($status) {
                'confirmed' => [
                    'Rendez-vous confirmé',
                    sprintf(
                        "Votre rendez-vous prévu le %s avec le docteur %s a bien été confirmé.",
                        $booking->getStartAt()->format('d/m/Y à H:i'),
                        $booking->getPro()->getUtilisateur()->getLastname()
                    )
                ],
                'cancelled' => [
                    'Rendez-vous annulé',
                    sprintf(
                        "Votre rendez-vous prévu le %s avec le docteur %s a été annulé.",
                        $booking->getStartAt()->format('d/m/Y à H:i'),
                        $booking->getPro()->getUtilisateur()->getLastname()
                    )
                ],
                default => throw new \InvalidArgumentException('Statut de réservation invalide.'),
            };

            $this->eventSubscriber->dispatch(
                new NotificationCreateEvent(
                    $title,
                    $message,
                    NotificationType::SIMPLE->value,
                    $booking->getUtilisateur()
                )
            );

            return $this->json(['success' => true]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

}
