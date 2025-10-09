<?php

namespace App\Controller\Pro;

use App\DTO\Pro\DeleteSchedulesDto;
use App\DTO\Pro\UpdateSchedulesDto;
use App\Entity\User;
use App\Service\ProService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class DeleteSchedulesPro extends AbstractController
{
    public function __construct(
        private readonly UtilitaireService $utilitaireService,
        private readonly ProService $proService,
    ){}
    #[Route('/api/delete-schedules', name: 'api_delete_schedules', methods: ['DELETE'])]
    public function __invoke(#[CurrentUser]User $user, Request $request): Response
    {
        try {
            if (!$user->getPro()) {
                return $this->json(['success' => false], Response::HTTP_FORBIDDEN);
            }

            $data = json_decode($request->getContent());

            $updateSchedulesDto = $this->utilitaireService->mapAndValidateRequestDto(
                $data,
                new DeleteSchedulesDto()
            );

            $this->proService->deleteSchedule($updateSchedulesDto);

            return $this->json(['success' => true], Response::HTTP_OK);
        } catch(\Throwable $e) {
            return $this->json(['success' => false, 'message'=>$e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }
}
