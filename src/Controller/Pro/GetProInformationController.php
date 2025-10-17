<?php

declare(strict_types=1);

namespace App\Controller\Pro;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GetProInformationController extends AbstractController
{
    #[Route('/api/get-pro-information', name: 'api_get_pro_information', methods: ['GET'])]
    #[IsGranted('ROLE_PRO')]
    public function __invoke(#[CurrentUser]User $user): Response
    {
        try {
            $pro = $user->getPro();

            $data = [
                'id' => $pro->getId(),
                'userId' => $user->getId(),
                'description' => $pro->getDescription(),
                'diplome' => $pro->getDiplome(),
                'price' => $pro->getPrice(),
                'country' => $pro->getCountry(),
                'city' => $pro->getCity(),
                'address' => $pro->getAddress(),
                'siren' => $pro->getSiren(),
                'siret' => $pro->getSiret(),
                'email' => $pro->getEmail(),
                'phone' => $pro->getPhone(),
                'title' => $pro->getTitle(),
                'updatedAt' => $pro->getUpdatedAt()->format(\DateTime::ATOM),
                'createdAt' => $pro->getCreatedAt()->format(\DateTime::ATOM),
            ];

            return $this->json($data);
        } catch (\Throwable $exception) {
            return $this->json(['success'=>false,'error' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
