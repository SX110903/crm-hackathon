<?php

declare(strict_types=1);

namespace App\Application\Commands\Evaluation;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\EvaluationRepositoryInterface;
use App\Models\Evaluation;

final class CreateEvaluationHandler implements CommandHandlerInterface
{
    public function __construct(private readonly EvaluationRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Evaluation
    {
        /** @var CreateEvaluationCommand $command */
        return $this->repository->create([
            'project_id'         => $command->projectId,
            'judge_id'           => $command->judgeId,
            'innovation_score'   => $command->innovationScore,
            'technical_score'    => $command->technicalScore,
            'presentation_score' => $command->presentationScore,
            'usability_score'    => $command->usabilityScore,
            'comments'           => $command->comments,
        ]);
    }
}
