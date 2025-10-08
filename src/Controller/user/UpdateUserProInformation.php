<?php

namespace App\Controller\user;

use App\DTO\user\UpdateUserProInformationDto;
use App\Entity\User;
use App\Service\UserService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UpdateUserProInformation extends AbstractController
{
    public function __construct(
        public readonly UtilitaireService $utilitaireService,
        public readonly UserService $userService
    ){}

    #[Route(path: '/api/user-pro', name: 'updateUserProInformation', methods: ['PUT'])]
    public function __invoke(#[CurrentUser]User $user,Request $request)
    {
        try {
            if (empty($user->getPro())) {
                return $this->json(['success' => false, 'message'=> 'You are not Pro'], 400);
            }

            $data = json_decode($request->getContent());

           $proInformationDto = $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new UpdateUserProInformationDto()
            );

            $this->userService->updateProInformation($proInformationDto,$user);

            return $this->json(['success'=>true],200);
        } catch (\Throwable $th) {
            return $this->json(['success'=>false,'message'=>$th->getMessage()],400);
        }
    }
}
