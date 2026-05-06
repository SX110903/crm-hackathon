<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Commands\Participant\CreateParticipantCommand;
use App\Application\Commands\Participant\DeleteParticipantCommand;
use App\Application\Commands\Participant\UpdateParticipantCommand;
use App\Application\CQRS\CommandBus;
use App\Application\CQRS\QueryBus;
use App\Application\Queries\Participant\GetParticipantByIdQuery;
use App\Application\Queries\Participant\GetParticipantsQuery;
use App\Http\Resources\ParticipantResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ParticipantController extends BaseApiController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->queryBus->dispatch(new GetParticipantsQuery(
            page:    (int) $request->get('page', 1),
            perPage: (int) $request->get('per_page', 15),
            search:  $request->get('search'),
        ));

        return $this->success(ParticipantResource::collection($result));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', 'unique:participants,email'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'university'    => ['nullable', 'string', 'max:255'],
            'major'         => ['nullable', 'string', 'max:255'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $participant = $this->commandBus->dispatch(new CreateParticipantCommand(
            firstName:   $data['first_name'],
            lastName:    $data['last_name'],
            email:       $data['email'],
            phone:       $data['phone'] ?? null,
            university:  $data['university'] ?? null,
            major:       $data['major'] ?? null,
            yearOfStudy: isset($data['year_of_study']) ? (int) $data['year_of_study'] : null,
        ));

        return $this->success(new ParticipantResource($participant), 201);
    }

    public function show(int $id): JsonResponse
    {
        $participant = $this->queryBus->dispatch(new GetParticipantByIdQuery($id));

        return $this->success(new ParticipantResource($participant));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'first_name'    => ['required', 'string', 'max:255'],
            'last_name'     => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', "unique:participants,email,{$id}"],
            'phone'         => ['nullable', 'string', 'max:50'],
            'university'    => ['nullable', 'string', 'max:255'],
            'major'         => ['nullable', 'string', 'max:255'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        $participant = $this->commandBus->dispatch(new UpdateParticipantCommand(
            id:          $id,
            firstName:   $data['first_name'],
            lastName:    $data['last_name'],
            email:       $data['email'],
            phone:       $data['phone'] ?? null,
            university:  $data['university'] ?? null,
            major:       $data['major'] ?? null,
            yearOfStudy: isset($data['year_of_study']) ? (int) $data['year_of_study'] : null,
        ));

        return $this->success(new ParticipantResource($participant));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->commandBus->dispatch(new DeleteParticipantCommand($id));

        return $this->noContent();
    }
}
