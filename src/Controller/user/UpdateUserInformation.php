<?php

namespace App\Controller\user;

use App\DTO\user\UpdateUserInformationDto;
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

class UpdateUserInformation extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly UserService $userService,
        private readonly EventDispatcherInterface $eventDispatcher
    ){}
    #[Route(path: '/api/user-information', name: 'updateUserInformation', methods: ['PUT'])]
    public function __invoke(#[CurrentUser]User $user,Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent());

          $userInformationDto =  $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new UpdateUserInformationDto()
            );

          $user = $this->userService->updateInformation($userInformationDto, $user);

          $this->eventDispatcher->dispatch(
              new NotificationCreateEvent(
                  "Modification de vos informations personelles",
                  "La modification des vos informations personelles est un succés",
                  NotificationType::SIMPLE->value,
                  $user
              )
          );

            return $this->json(['success' => true], 201);
        }catch(\Throwable $e){
            return $this->json($e->getMessage(), 500);
        }
    }
}
