<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\Booking;
use App\Entity\User;
use App\Service\NewsletterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GetDashboardInformation extends AbstractController
{

    public function __construct(
        private readonly NewsletterService $newsletterService,
    ){

    }

    #[Route('/api/get-dashboard-information', name: 'get-dashboard-information', methods: ['GET'])]
    public function __invoke(#[CurrentUser]User $user): Response
    {
        try {

            $bookings = $user->getBookings();
            $now = new \DateTime();

            $bookingPast = $bookings->filter(function (Booking $booking) use ($now) {
                return $booking->getStartAt() < $now;
            });

            $bookingFuture = $bookings->filter(function (Booking $booking) use ($now) {
                return $booking->getStartAt() > $now;
            });
            $favorite = $user->getFavorites()->count();

            $data = [
                'bookingFutur'=>$bookingFuture->count(),
                'bookingPast'=>$bookingPast->count(),
                'praticianFavorite'=>$favorite,
                'newsletter'=>$this->newsletterService->isNewsletterExist($user->getEmail()),
            ];

            return $this->json($data, 201);

        } catch( \Throwable $throwable) {
            return $this->json(['success'=>false, 'message'=>$throwable->getMessage()], 400);
        }
    }
}
