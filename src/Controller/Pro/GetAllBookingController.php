<?php

declare(strict_types=1);

namespace App\Controller\Pro;

use App\Entity\User;
use App\Service\ProService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GetAllBookingController extends AbstractController
{
    public function __construct(
        private readonly ProService $proService,
    ){}
    #[Route('/api/get-all-booking', name: 'get-all-booking', methods: ['GET'])]
    public function __invoke(#[CurrentUser]User $user): Response
    {
        try {
            $bookings = $this->proService->returnAllBooking($user);

            $bookingArray = [];
            foreach ($bookings as $booking) {
                $bookingArray[] = [
                    'id' => $booking->getId(),
                    'pro' => $booking->getPro()->getId(),
                    'user' => [
                        'id' => $booking->getUtilisateur()->getId(),
                        'firstName' => $booking->getUtilisateur()->getFirstName(),
                        'lastName' => $booking->getUtilisateur()->getLastName(),
                        'email' => $booking->getUtilisateur()->getEmail(),
                        'phone' => $booking->getUtilisateur()->getPhone(),
                    ],
                    'startAt' => $booking->getStartAt()->format('Y-m-d H:i:s'),
                    'endAt' => $booking->getEndAt()->format('Y-m-d H:i:s'),
                    'note' => $booking->getNote(),
                    'status' => $booking->getStatus(),
                ];
            }

            return $this->json($bookingArray, Response::HTTP_OK);
        } catch(\Throwable $e){
            return $this->json(['success'=>false,'message' => $e->getMessage()], 500);
        }
    }
}
