<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Interface\DtoInterface;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class ChatService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ConversationRepository $conversationRepository,
        private readonly MessageRepository $messageRepository,
        private readonly UserRepository $userRepository,
    ) {}

    public function createConversation(DtoInterface $dto, User $user): Conversation
    {
        if ($user->getId() === intval($dto->user2)) {
            Throw new \Exception('You can\'t create a conversation with yourself.');
        }

        $user2 = $this->userRepository->find($dto->user2);

        if (!$user2) {
            Throw new \Exception('User cible not found');
        }

       $conversationAlreadyExiste = $user->getConversations()->filter(function (Conversation $conversation) use ($user2) {
            return $conversation->getUser2() === $user2 || $conversation->getUser1() === $user2;
        });


       if ($conversationAlreadyExiste->count() > 0) {
           Throw new \Exception('Conversation already existed');
       }

       $conversation = new Conversation();
       $conversation->setUser1($user);
       $conversation->setUser2($user2);
       $conversation->setCreatedAt(new \DateTimeImmutable());

       $this->entityManager->persist($conversation);
       $this->entityManager->flush();

       $this->entityManager->refresh($conversation);

       $message = new Message();
       $message->setConversation($conversation);
       $message->setText($dto->text);
       $message->setCreatedAt(new \DateTimeImmutable());
       $message->setSender($user);

       $this->entityManager->persist($message);
       $this->entityManager->flush();

       return $conversation;
    }

    public function createMessage(DtoInterface $dto, User $user) {
        $conversation = $this->conversationRepository->find($dto->conversationId);

        if (!$conversation) {
            Throw new \Exception('Conversation not found');
        }

        $message = new Message();
        $message->setConversation($conversation);
        $message->setSender($user);
        $message->setText($dto->text);
        $message->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }

    public function getConversation(User $user, int $conversationId): array
    {
        $messages = $this->messageRepository->findBy(['conversation' => $conversationId], ['createdAt' => 'ASC']);

        if (!$messages) {
            throw new \Exception('No messages found');
        }

        $data = [];
        foreach ($messages as $msg) {
            $data[] = [
                'id' => $msg->getId(),
                'content' => $msg->getText(),
                'sender' => [
                    'id' => $msg->getSender()->getId(),
                    'name' => $msg->getSender()->getLastname() . ' ' . $msg->getSender()->getFirstname(),
                ],
                'createdAt' => $msg->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        return $data;
    }

}
