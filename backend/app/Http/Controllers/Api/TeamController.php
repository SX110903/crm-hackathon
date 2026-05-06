<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Commands\Team\AddTeamMemberCommand;
use App\Application\Commands\Team\CreateTeamCommand;
use App\Application\Commands\Team\DeleteTeamCommand;
use App\Application\Commands\Team\RemoveTeamMemberCommand;
use App\Application\Commands\Team\UpdateTeamCommand;
use App\Application\CQRS\CommandBus;
use App\Application\CQRS\QueryBus;
use App\Application\Queries\Team\GetTeamByIdQuery;
use App\Application\Queries\Team\GetTeamsQuery;
use App\Http\Resources\TeamResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class TeamController extends BaseApiController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->queryBus->dispatch(new GetTeamsQuery(
            page:    (int) $request->get('page', 1),
            perPage: (int) $request->get('per_page', 15),
            search:  $request->get('search'),
        ));

        return $this->success(TeamResource::collection($result));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:teams,name'],
            'max_members' => ['nullable', 'integer', 'min:1', 'max:127'],
            'leader_id'   => ['nullable', 'integer', 'exists:participants,id'],
        ]);

        $team = $this->commandBus->dispatch(new CreateTeamCommand(
            name:       $data['name'],
            maxMembers: (int) ($data['max_members'] ?? 10),
            leaderId:   isset($data['leader_id']) ? (int) $data['leader_id'] : null,
        ));

        return $this->success(new TeamResource($team), 201);
    }

    public function show(int $id): JsonResponse
    {
        $team = $this->queryBus->dispatch(new GetTeamByIdQuery($id));

        return $this->success(new TeamResource($team));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', "unique:teams,name,{$id}"],
            'max_members' => ['nullable', 'integer', 'min:1', 'max:127'],
            'leader_id'   => ['nullable', 'integer', 'exists:participants,id'],
        ]);

        $team = $this->commandBus->dispatch(new UpdateTeamCommand(
            id:         $id,
            name:       $data['name'],
            maxMembers: (int) ($data['max_members'] ?? 10),
            leaderId:   isset($data['leader_id']) ? (int) $data['leader_id'] : null,
        ));

        return $this->success(new TeamResource($team));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->commandBus->dispatch(new DeleteTeamCommand($id));

        return $this->noContent();
    }

    public function members(int $id): JsonResponse
    {
        $team = $this->queryBus->dispatch(new GetTeamByIdQuery($id));

        return $this->success($team->members()->with('participant')->get());
    }

    public function addMember(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'participant_id' => ['required', 'integer', 'exists:participants,id'],
            'role'           => ['nullable', 'string', 'max:255'],
        ]);

        $member = $this->commandBus->dispatch(new AddTeamMemberCommand(
            teamId:        $id,
            participantId: (int) $data['participant_id'],
            role:          $data['role'] ?? 'Developer',
        ));

        return $this->success($member, 201);
    }

    public function removeMember(int $id, int $participantId): JsonResponse
    {
        $this->commandBus->dispatch(new RemoveTeamMemberCommand(
            teamId:        $id,
            participantId: $participantId,
        ));

        return $this->noContent();
    }
}
