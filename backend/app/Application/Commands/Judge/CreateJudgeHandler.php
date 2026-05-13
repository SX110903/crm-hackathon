<?php

declare(strict_types=1);

namespace App\Application\Commands\Judge;

use App\Application\CQRS\Contracts\CommandHandlerInterface;
use App\Application\CQRS\Contracts\CommandInterface;
use App\Domain\Repositories\JudgeRepositoryInterface;
use App\Models\Judge;

final class CreateJudgeHandler implements CommandHandlerInterface
{
    public function __construct(private readonly JudgeRepositoryInterface $repository) {}

    public function handle(CommandInterface $command): Judge
    {
        /** @var CreateJudgeCommand $command */
        return $this->repository->create([
            'first_name'          => $command->firstName,
            'last_name'           => $command->lastName,
            'email'               => $command->email,
            'company'             => $command->company,
            'expertise'           => $command->expertise,
            'years_of_experience' => $command->yearsOfExperience,
        ]);
    }
}
