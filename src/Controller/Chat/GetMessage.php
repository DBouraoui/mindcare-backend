<?php

declare(strict_types=1);

namespace App\Controller\Chat;

use App\Entity\User;
use App\Service\ChatService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GetMessage extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly ChatService $chatService,
    ){}

    #[Route('/api/messages/{conversationId}', name: 'chat_get_message', methods: ['GET'])]
    public function __invoke(#[CurrentUser]User $user, $conversationId): Response
    {
        try {
            $conversation = $this->chatService->getConversation($user, intval($conversationId));

            return $this->json($conversation);
        } catch (\Throwable $e) {
            return $this->json(['success'=>false, 'message' => $e->getMessage()], 500);
        }
    }
}
