<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class GetUserInformation extends AbstractController
{
    #[Route('/api/get-user-information', name: 'api_get_user_information', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function __invoke(#[CurrentUser]User $user): Response
    {
        try {
            $data = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'phone' => $user->getPhone(),
                'city' => $user->getCity(),
                'isActive'=> $user->isActive(),
                'createdAt' => $user->getCreatedAt()?->format(\DateTime::ATOM),
                'updatedAt' => $user->getUpdatedAt()?->format(\DateTime::ATOM),
            ];

            return $this->json($data);
        } catch( \Throwable $e) {
            return $this->json(['succes'=>false,'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
