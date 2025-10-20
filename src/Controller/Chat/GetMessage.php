<?php

declare(strict_types=1);

namespace App\Controller\Chat;

use App\Entity\Conversation;
use App\Entity\User;
use App\Service\ChatService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GetMessage extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly ChatService $chatService,
    ){}

    #[Route('/api/messages/{conversationId}', name: 'chat_get_message', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(#[CurrentUser]User $user, $conversationId): Response
    {
        try {
            $conversation = $this->chatService->getConversation($user, intval($conversationId));

            return $this->json($conversation);
        } catch (\Throwable $e) {
            return $this->json(['success'=>false, 'message' => $e->getMessage()], 500);
        }
    }

    #[Route('/api/get-conversation', name: 'chat_get_conversation', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getConversation(#[CurrentUser]User $user): Response
    {
        try {

            $conversationCollection = $this->chatService->getListingConversations($user);

            $conversation = array_map(static function ($conversation) use ($user) {
                // Déterminer l'autre utilisateur
                $userToDisplay = $conversation->getUser1() === $user
                    ? $conversation->getUser2()
                    : $conversation->getUser1();

                // Récupérer le dernier message
                $messages = $conversation->getMessages()->toArray();
                $lastMessage = !empty($messages) ? end($messages) : null;

                return [
                    'id' => strval($conversation->getId()),
                    'firstname' => $userToDisplay->getFirstname(),
                    'lastname' => $userToDisplay->getLastname(),
                    'lastMessage' => $lastMessage ? [
                        'lastSenderlastname' => $lastMessage->getSender()->getLastname(),
                        'lastSenderfirstname' => $lastMessage->getSender()->getFirstname(),
                        'message' => $lastMessage->getText(),
                    ] : null,
                    'createdAt' => $conversation->getCreatedAt()->format(\DateTime::ATOM),
                ];
            }, $conversationCollection);

            return $this->json($conversation);


        } catch (\Throwable $e) {
            return $this->json(['success'=>false, 'message' => $e->getMessage()], 500);
        }
    }
}
