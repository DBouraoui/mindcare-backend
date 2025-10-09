<?php

namespace App\Service;

use App\Entity\SchedulesPro;
use App\Entity\User;
use App\Interface\DtoInterface;
use App\Repository\SchedulesProRepository;
use Doctrine\ORM\EntityManagerInterface;
readonly class ProService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SchedulesProRepository $schedulesProRepository,
    ){}

    public function createSchedule(DtoInterface $dto, User $user): SchedulesPro
    {
            $schedule = $this->schedulesProRepository->findOneBy(['day'=> $dto->day, 'pro' => $user->getPro()]);

            if (!empty($schedule)) {
                Throw new \Exception('Schedule already exists for this day');
            }

            $schedule = new SchedulesPro();
            $schedule->setPro($user->getPro());
            $schedule->setDay($dto->day);
            $schedule->setMorningStart($dto->morningStart ?? null);
            $schedule->setMorningEnd($dto->morningEnd ?? null);
            $schedule->setAfternoonStart($dto->afternoonStart ?? null);
            $schedule->setAfternoonEnd($dto->afternoonEnd ?? null);
            $schedule->setClosed($dto->closed ?? null);
            $schedule->setUpdatedAt(new \DateTimeImmutable());

            $this->entityManager->persist($schedule);
            $this->entityManager->flush();

            return $schedule;
    }

    public function updateSchedule(DtoInterface $dto): SchedulesPro
    {
        $schedule = $this->schedulesProRepository->find($dto->id);

        if (empty($schedule)) {
            Throw new \Exception('No schedule exists for this day');
        }

        $schedule->setDay($dto->day);
        $schedule->setMorningStart($dto->morningStart ?? null);
        $schedule->setMorningEnd($dto->morningEnd ?? null);
        $schedule->setAfternoonStart($dto->afternoonStart ?? null);
        $schedule->setAfternoonEnd($dto->afternoonEnd ?? null);
        $schedule->setClosed($dto->closed ?? null);
        $schedule->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        return $schedule;
    }

    public function deleteSchedule(DtoInterface $dto): bool
    {
        $schedule = $this->schedulesProRepository->find($dto->id);

        if (empty($schedule)) {
            Throw new \Exception('No schedule exists for this day');
        }

        $this->entityManager->remove($schedule);
        $this->entityManager->flush();

        return true;
    }
}
