<?php

declare(strict_types=1);

namespace App\Application\Commands\Participant;

use App\Application\CQRS\Contracts\CommandInterface;

final class UpdateParticipantCommand implements CommandInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $university,
        public readonly ?string $major,
        public readonly ?int $yearOfStudy,
    ) {}
}
