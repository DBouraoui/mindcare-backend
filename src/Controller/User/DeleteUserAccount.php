<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Service\UserService;
use App\Service\UtilitaireService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DeleteUserAccount extends AbstractController
{
    public function __construct(
        private readonly userService $userService,
        private readonly UtilitaireService $utilitaireService,
        private readonly LoggerInterface $logger
    ){}

    #[Route(path: '/api/user', name: 'userDeleteAccount', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(#[CurrentUser]User $user){
        try {
            $this->userService->deleteUser($user);

            $this->utilitaireService->sendEmail(
                "Votre compte à été supprimé",
                $user->getEmail(),
                "User/DeleteAccount",
                [
                    "app_url" => $_ENV['FRONT_URL'],
                    "user" => $user,
                ]
            );

            $this->logger->log(1,sprintf("L'utilisateur %s as supprimer sont compte", $user->getEmail()));

            return $this->json(['success'=>true],200);
        } catch(\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

}
