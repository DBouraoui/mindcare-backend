<?php

namespace App\Controller\user;

use App\DTO\user\UpdateUserEmailDto;
use App\Entity\User;
use App\Enum\NotificationType;
use App\Event\NotificationCreateEvent;
use App\Service\UserService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UpdateUserEmail extends AbstractController
{
    public function __construct(
        public readonly UtilitaireService $utilitaireService,
        public readonly UserService $userService,
        private readonly EventSubscriberInterface $eventSubscriber
    ){}

    #[Route(path: '/api/user-email', name: 'updateUserEmail', methods: ['PATCH'])]
    public function __invoke(#[CurrentUser]User $user, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent());

           $userEmailDto= $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new UpdateUserEmailDto()
            );

           $this->userService->updateEmail($userEmailDto, $user);

           $this->eventSubscriber->dispatch(
               new NotificationCreateEvent(
                   "Modification de l'adresse email",
                   "L'addresse email à été modifier avec succés",
                   NotificationType::WARNING->value,
                   $user
               )
           );

           //todo send email and notif for update
            return $this->json(['success'=>true],200);
        } catch (\Throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }
}
