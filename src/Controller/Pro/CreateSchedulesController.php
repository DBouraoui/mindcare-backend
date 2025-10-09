<?php

declare(strict_types=1);

namespace App\Controller\Pro;

use App\DTO\Pro\CreateSchedulesDto;
use App\Entity\User;
use App\Service\ProService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CreateSchedulesController extends AbstractController
{

    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly ProService $proService,
    ){}
    #[Route('/api/create-schedules', name: 'pro_create_schedules', methods: ['POST'])]
    public function __invoke(#[CurrentUser]User $user, Request $request): Response
    {
        try {
            if (!$user->getPro()) {
                return $this->json(['success' => false], Response::HTTP_FORBIDDEN);
            }

            $data = json_decode($request->getContent());

           $schedulesDto = $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new CreateSchedulesDto()
            );

           $this->proService->createSchedule($schedulesDto, $user);

            return $this->json(['success' => true], 201);
        } catch(\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
