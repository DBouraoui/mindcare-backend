<?php

declare(strict_types=1);

namespace App\Controller\Pro;

use App\Entity\User;
use App\Service\ProService;
use App\Service\UtilitaireService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GetSchedulesPro extends AbstractController
{
    #[Route('/api/get-schedules', name: 'api_delete_schedules', methods: ['GET'])]
    #[IsGranted('ROLE_PRO')]
    public function __invoke(#[CurrentUser]User $user): Response
    {
        try {
                $pro = $user->getPro();

            $schedulesCollections = $pro->getSchedulesPros();

            $scheduleArray = [];

            foreach ($schedulesCollections as $schedule) {
                // Si le jour est fermé, on renvoie juste les infos minimales
                if ($schedule->getClosed()) {
                    $scheduleArray[] = [
                        'id' => $schedule->getId(),
                        'day' => $schedule->getDay(),
                        'closed' => true,
                        'morning' => null,
                        'afternoon' => null,
                        'updatedAt' => $schedule->getUpdatedAt()?->format(\DateTime::ATOM),
                    ];
                    continue;
                }

                $scheduleArray[] = [
                    'id' => $schedule->getId(),
                    'day' => $schedule->getDay(),
                    'closed' => false,
                    'morning' => [
                        'start' => $schedule->getMorningStart(),
                        'end' => $schedule->getMorningEnd(),
                    ],
                    'afternoon' => [
                        'start' => $schedule->getAfternoonStart(),
                        'end' => $schedule->getAfternoonEnd(),
                    ],
                    'updatedAt' => $schedule->getUpdatedAt()?->format(\DateTime::ATOM),
                ];
            }


            return $this->json($scheduleArray, Response::HTTP_OK);
        } catch(\Throwable $e) {
            return $this->json(['success' => false, 'message'=>$e->getMessage()], Response::HTTP_FORBIDDEN);
        }
    }
}
