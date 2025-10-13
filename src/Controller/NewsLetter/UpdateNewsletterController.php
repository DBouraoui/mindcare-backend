<?php

declare(strict_types=1);

namespace App\Controller\NewsLetter;

use App\Entity\User;
use App\Service\NewsletterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UpdateNewsletterController extends AbstractController
{
    public function __construct(
        private readonly NewsletterService $newsletterService,
    ){}
    #[Route('/api/newsletter', name: 'api_update_newsletter', methods: ['PATCH'])]
    public function __invoke(#[CurrentUser]User $user): Response
    {
        try{
            $this->newsletterService->updateNewsletter($user);

            return $this->json(['success' => true]);
        } catch(\Throwable $e) {
            return $this->json(['success'=>false,'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
