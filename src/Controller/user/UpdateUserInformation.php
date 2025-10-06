<?php

namespace App\Controller\user;

use App\DTO\user\UpdateUserInformationDto;
use App\DTO\user\UpdateUserPasswordDto;
use App\Entity\User;
use App\Service\AuthService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UpdateUserInformation extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly AuthService $authService,
    ){}
    #[Route(path: '/api/user', name: 'updateUserInformation', methods: ['PUT'])]
    public function updateInformation(#[CurrentUser]User $user,Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent());

          $userInformationDto =  $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new UpdateUserInformationDto()
            );

          $user = $this->authService->updateInformation($userInformationDto, $user);

            return $this->json(['success' => true], 201);
        }catch(\Throwable $e){
            return $this->json($e->getMessage(), 500);
        }
    }

    #[Route(path: '/api/user-password', name: 'updateUserInformation', methods: ['PATCH'])]
    public function updatePassword(#[CurrentUser]User $user,Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent());

            $userInformationDto =  $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new UpdateUserPasswordDto()
            );

            $user = $this->authService->updateUserPassword($userInformationDto, $user);

            return $this->json(['success' => true], 201);
        }catch(\Throwable $e){
            return $this->json($e->getMessage(), 500);
        }
    }

}
