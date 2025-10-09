<?php

namespace App\DTO\Pro;

use App\Interface\DtoInterface;

class UpdateSchedulesDto implements DtoInterface
{
    public string $id;
    public string $day;
    public string $morningStart;
    public string $morningEnd;
    public string $afternoonStart;
    public string $afternoonEnd;
    public string $closed;
}
