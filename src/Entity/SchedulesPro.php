<?php

namespace App\Entity;

use App\Repository\SchedulesProRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SchedulesProRepository::class)]
class SchedulesPro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'schedulesPros')]
    private ?Pro $pro = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $day = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $morningStart = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $morningEnd = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $afternoonStart = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $afternoonEnd = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $closed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPro(): ?Pro
    {
        return $this->pro;
    }

    public function setPro(?Pro $pro): static
    {
        $this->pro = $pro;

        return $this;
    }

    public function getDay(): ?string
    {
        return $this->day;
    }

    public function setDay(?string $day): static
    {
        $this->day = $day;

        return $this;
    }

    public function getMorningStart(): ?string
    {
        return $this->morningStart;
    }

    public function setMorningStart(?string $morningStart): static
    {
        $this->morningStart = $morningStart;

        return $this;
    }

    public function getMorningEnd(): ?string
    {
        return $this->morningEnd;
    }

    public function setMorningEnd(?string $morningEnd): static
    {
        $this->morningEnd = $morningEnd;

        return $this;
    }

    public function getAfternoonStart(): ?string
    {
        return $this->afternoonStart;
    }

    public function setAfternoonStart(?string $afternoonStart): static
    {
        $this->afternoonStart = $afternoonStart;

        return $this;
    }

    public function getAfternoonEnd(): ?string
    {
        return $this->afternoonEnd;
    }

    public function setAfternoonEnd(?string $afternoonEnd): static
    {
        $this->afternoonEnd = $afternoonEnd;

        return $this;
    }

    public function getClosed(): ?string
    {
        return $this->closed;
    }

    public function setClosed(?string $closed): static
    {
        $this->closed = $closed;

        return $this;
    }
}
