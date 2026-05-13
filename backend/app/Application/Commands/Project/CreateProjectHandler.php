<?php

declare(strict_types=1);

namespace App\Application\Commands\Project;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\ProjectRepositoryInterface;
use App\Models\Project;

final class CreateProjectHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ProjectRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Project
    {
        /** @var CreateProjectCommand $command */
        return $this->repository->create([
            'team_id'          => $command->teamId,
            'name'             => $command->name,
            'description'      => $command->description,
            'category'         => $command->category,
            'technology_stack' => $command->technologyStack,
            'github_url'       => $command->githubUrl,
            'demo_url'         => $command->demoUrl,
            'status'           => $command->status,
        ]);
    }
}
