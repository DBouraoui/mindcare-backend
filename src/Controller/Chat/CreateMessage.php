<?php

declare(strict_types=1);

namespace App\Controller\Chat;

use App\DTO\Chat\CreateMessageDto;
use App\Entity\User;
use App\Service\ChatService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CreateMessage extends AbstractController
{

    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly ChatService $chatService,
    ){}

    #[Route('/api/create-message', name: 'api_create_message', methods: ['POST'])]
    public function __invoke(#[CurrentUser]User $user, Request $request): Response
    {
        try {
            $data = json_decode($request->getContent());

            $createMessageDto = $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new CreateMessageDto()
            );

            $this->chatService->createMessage($createMessageDto, $user);

            return $this->json(['success'=>true, 'message'=>'Message send'], 201);
        } catch(\Throwable $e) {
            return $this->json(['success'=>false, 'message'=>$e->getMessage()], 500);
        }
    }
}
