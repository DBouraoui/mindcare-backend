<?php

declare(strict_types=1);

namespace App\Controller\Pro;

use App\DTO\Pro\UpdateSchedulesDto;
use App\Entity\User;
use App\Service\ProService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class UpdateSchedulesPro extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly ProService $proService,
    ){}
    #[Route('/api/update-schedules', name: 'api_update_schedules', methods: ['PUT'])]
    public function __invoke(#[CurrentUser]User $user, Request $request): Response
    {
        try {
            if (!$user->getPro()) {
                return $this->json(['success' => false], Response::HTTP_FORBIDDEN);
            }

            $data = json_decode($request->getContent());

           $updateSchedulesDto = $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new UpdateSchedulesDto()
            );

           $this->proService->updateSchedule($updateSchedulesDto);

           return $this->json(['success' => true], Response::HTTP_OK);
        } catch(\Throwable $e) {
            return $this->json(['success' => false, 'message'=>$e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }
}
