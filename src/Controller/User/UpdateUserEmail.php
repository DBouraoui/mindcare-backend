<?php

namespace App\Controller\User;

use App\DTO\User\UpdateUserEmailDto;
use App\Entity\User;
use App\Enum\NotificationType;
use App\Event\NotificationCreateEvent;
use App\Service\UserService;
use App\Service\UtilitaireService;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UpdateUserEmail extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly UserService $userService,
        private readonly EventDispatcherInterface $eventSubscriber
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
            return $this->json(['success'=>true],200);
        } catch (\Throwable $e) {
            return $this->json($e->getMessage(), 500);
        }
    }
}
