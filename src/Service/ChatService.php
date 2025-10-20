<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Interface\DtoInterface;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use App\Repository\ProRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class ChatService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ConversationRepository $conversationRepository,
        private readonly MessageRepository      $messageRepository,
        private ProRepository $proRepository,
    ) {}

    public function createConversation(DtoInterface $dto, User $user): Conversation
    {
        $pro = $this->proRepository->find($dto->user2);
        $user2 = $pro->getUtilisateur();

        if ($user->getId() === intval($user2->getId())) {
            Throw new \Exception('You can\'t create a conversation with yourself.');
        }



        if (!$user2) {
            Throw new \Exception('Pro cible not found');
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
            $idsender = $user->getId() === $msg->getSender()->getId() ? "me" : $msg->getSender()->getId();

            $data[] = [
                'id' => strval($msg->getId()),
                'content' => $msg->getText(),
                'sender' => [
                    'id' => $msg->getSender()->getId() === $user->getId() ? 'me' : strval($msg->getSender()->getId()),
                    'name' => $msg->getSender()->getLastname() . ' ' . $msg->getSender()->getFirstname(),
                ],
                'createdAt' => $msg->getCreatedAt()->format(\DateTime::ATOM),
            ];

        }

        return $data;
    }

    public function getListingConversations(User $user)
    {
       $conversation = $this->conversationRepository->createQueryBuilder('conversation')
            ->where('conversation.user1 = :user')
            ->orWhere('conversation.user2 = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

       return $conversation;
    }
}
