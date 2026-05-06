<?php

declare(strict_types=1);

namespace App\Application\Commands\Judge;

use App\Application\CQRS\Contracts\CommandInterface;

final class DeleteJudgeCommand implements CommandInterface
{
    public function __construct(public readonly int $id) {}
}
