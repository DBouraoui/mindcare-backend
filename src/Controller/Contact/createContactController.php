<?php

declare(strict_types=1);

namespace App\Controller\Contact;

use App\DTO\Contact\CreateContactDto;
use App\Service\ContactService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class createContactController extends AbstractController
{

    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly ContactService $contactService,
    ){}
    #[Route('/api/create-contact', name: 'api_create_contact', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        try {
            $data = json_decode($request->getContent());

            $contactDto = $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new CreateContactDto()
            );

            $this->contactService->createContact($contactDto);

            return $this->json(['success' => true]);
        } catch(\Exception $e) {
            return $this->json(['success' => false,'error' => $e->getMessage()]);
        }
    }
}
