<?php

namespace App\Controller\user;

use App\DTO\user\UpdateUserPasswordDto;
use App\Entity\User;
use App\Enum\NotificationType;
use App\Event\NotificationCreateEvent;
use App\Service\UserService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UpdateUserPassword extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly UserService $userService,
        private readonly EventSubscriberInterface $eventSubscriber
    ){}

    #[Route(path: '/api/user-password', name: 'updateUserInformation', methods: ['PATCH'])]
    public function __invoke(#[CurrentUser]User $user,Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent());

            $userInformationDto =  $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new UpdateUserPasswordDto()
            );

            $this->userService->updateUserPassword($userInformationDto, $user);

            $this->eventSubscriber->dispatch(
                new NotificationCreateEvent(
                    "Modification de mot de passe",
                    "La modification de votre mot de passe a été réaliser avec succes",
                    NotificationType::WARNING->value,
                    $user
                )
            );

            return $this->json(['success' => true], 201);
        }catch(\Throwable $e){
            return $this->json($e->getMessage(), 500);
        }
    }
}
