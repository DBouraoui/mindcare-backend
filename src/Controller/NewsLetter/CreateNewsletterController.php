<?php

declare(strict_types=1);

namespace App\Controller\NewsLetter;

use App\Service\NewsletterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CreateNewsletterController extends AbstractController
{
    public function __construct(
        private readonly NewsletterService $newsletterService,
    ){}
    #[Route('/api/create-newsletter', name: 'api_create_newsletter', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        try{
            $data = json_decode($request->getContent());

            $this->newsletterService->createNewsletter($data->email);

            return $this->json(['success' => true]);
        } catch(\Throwable $e) {
            return $this->json(['success'=>false,'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
