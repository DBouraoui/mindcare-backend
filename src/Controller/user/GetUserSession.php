<?php

namespace App\Controller\user;

use App\Entity\Session;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class GetUserSession extends AbstractController
{
    #[Route(path: '/api/user-session', name: 'getUserConnexion', methods: ['GET'])]
    public function __invoke(#[CurrentUser]User $user){
        try {
            $logs = $user->getSession()->map(function(Session $session){
                return [
                    'id' => $session->getId(),
                    'login' => $session->getIp(),
                    'created_at'=>$session->getLoggedAt()->format('d-m-y H:i:s'),
                    'agent'=> $session->getUserAgent()
                ];
            });

            return $this->json($logs,200);
        } catch (\Throwable $e) {
            return $this->json(['success'=>false,'message'=>$e->getMessage()],500);
        }
    }

}
