<?php

declare(strict_types=1);

namespace App\Application\Commands\Evaluation;

use App\Application\CQRS\Contracts\CommandInterface;

final class CreateEvaluationCommand implements CommandInterface
{
    public function __construct(
        public readonly int $projectId,
        public readonly int $judgeId,
        public readonly float $innovationScore,
        public readonly float $technicalScore,
        public readonly float $presentationScore,
        public readonly float $usabilityScore,
        public readonly ?string $comments,
    ) {}
}
