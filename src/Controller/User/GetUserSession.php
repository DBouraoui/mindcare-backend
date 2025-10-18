<?php

namespace App\Controller\User;

use App\Entity\Session;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GetUserSession extends AbstractController
{
    #[Route(path: '/api/user-session', name: 'getUserConnexion', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(#[CurrentUser]User $user){
        try {
            $logs = $user->getSession()->map(function(Session $session){
                return [
                    'id' => $session->getId(),
                    'login' => $session->getIp(),
                    'createdAt'=>$session->getLoggedAt()->format(\DateTime::ATOM),
                    'agent'=> $session->getUserAgent()
                ];
            })->toArray();

            usort($logs, function ($a, $b) {
                return $b['createdAt'] <=> $a['createdAt'];
            });

            return $this->json($logs,200);
        } catch (\Throwable $e) {
            return $this->json(['success'=>false,'message'=>$e->getMessage()],500);
        }
    }

}
