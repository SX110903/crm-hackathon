<?php

declare(strict_types=1);

namespace App\Application\Commands\Mentor;

use App\Application\CQRS\Contracts\CommandInterface;

final class UpdateMentorCommand implements CommandInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly ?string $company,
        public readonly ?string $specialization,
        public readonly ?int $availableSlots,
    ) {}
}
