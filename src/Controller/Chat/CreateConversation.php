<?php

namespace App\Controller\Chat;

use App\DTO\Chat\CreateConversationDto;
use App\Entity\User;
use App\Service\ChatService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CreateConversation extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly ChatService $chatService,
    ){}

    #[Route('/api/create-conversation', name: 'api_create_conversation', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(#[CurrentUser]User $user, Request $request)
    {
        try {
            $data = json_decode($request->getContent());

            $createConversationDto =$this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new CreateConversationDto()
            );

            $this->chatService->createConversation($createConversationDto, $user);

            return $this->json(['success'=>true, 'message'=>'Conversation créer'], 201);
        } catch(\Throwable $e) {
            return $this->json(['success'=>false, 'message'=>$e->getMessage()], 500);
        }
    }

}
