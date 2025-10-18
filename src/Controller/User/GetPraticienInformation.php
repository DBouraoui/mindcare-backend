<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class GetPraticienInformation extends AbstractController
{
    public function __construct(private readonly UserService $userService)
    {
    }

    #[Route('/api/get-praticien-listing', name: 'api_get_praticien_listing', methods: ['GET'])]
    public function __invoke(#[MapQueryParameter]string $query): Response
    {
        try {
            $praticienObject = $this->userService->getPraticienListing($query);

            $array = [];
            foreach ($praticienObject as $praticien) {
                $array[] = [
                    'id' => $praticien->getId(),
                    'title'=> $praticien->getTitle(),
                    'description' => $praticien->getDescription(),
                    'city' => $praticien->getCity(),
                    'address' => $praticien->getAddress(),
                ];
            }

            return $this->json($array);
        } catch(\Throwable $e){
            return $this->json(['success'=>false,'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/get-praticien-information/{id}', name: 'api_get_all_praticien_information', methods: ['GET'])]
    public function allInformation(string $id): Response
    {
        try {
            $praticienObject = $this->userService->getPraticienById(intval($id));

            $horrairesObject = $praticienObject->getSchedulesPros();

            $horraires = [];
            foreach ($horrairesObject as $horraire) {
                $horraires[] = [
                    'id' => $horraire->getId(),
                    'day' => $horraire->getDay(),
                    'closed' => $horraire->getClosed(),
                    'morning' => [
                        'start' => $horraire->getMorningStart(),
                        'end' => $horraire->getMorningEnd(),
                    ],
                    'afternoon' => [
                        'start' => $horraire->getAfternoonStart(),
                        'end' => $horraire->getAfternoonEnd(),
                    ],
                    'updatedAt' => $horraire->getUpdatedAt()?->format(\DateTime::ATOM),
                ];
            }

            $array = [
                'id' => $praticienObject->getId(),
                'userId' => $praticienObject->getUtilisateur()->getId(),
                'description' => $praticienObject->getDescription(),
                'diplome' => $praticienObject->getDiplome(),
                'price' => $praticienObject->getPrice(),
                'country' => $praticienObject->getCountry(),
                'city' => $praticienObject->getCity(),
                'address' => $praticienObject->getAddress(),
                'siren' => $praticienObject->getSiren(),
                'siret' => $praticienObject->getSiret(),
                'email' => $praticienObject->getEmail(),
                'phone' => $praticienObject->getPhone(),
                'title' => $praticienObject->getTitle(),
                'updatedAt' => $praticienObject->getUpdatedAt()->format(\DateTime::ATOM),
                'createdAt' => $praticienObject->getCreatedAt()->format(\DateTime::ATOM),
                'horraires' => $horraires,
            ];

            return $this->json($array);
        } catch(\Throwable $e){
            return $this->json(['success'=>false,'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
