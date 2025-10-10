<?php

declare(strict_types=1);

namespace App\Controller\Pro;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ValidateBookingController extends AbstractController
{
    public function __construct(){}

    #[Route('/api/validate-booking', name: 'validate-booking', methods: ['POST'])]
    public function __invoke(): Response
    {
        return $this->render('validate_booking/index.html.twig');
    }
}
