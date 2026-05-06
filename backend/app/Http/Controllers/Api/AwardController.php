<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Commands\Award\AssignAwardCommand;
use App\Application\Commands\Award\CreateAwardCommand;
use App\Application\Commands\Award\DeleteAwardCommand;
use App\Application\Commands\Award\UpdateAwardCommand;
use App\Application\CQRS\CommandBus;
use App\Application\CQRS\QueryBus;
use App\Application\Queries\Award\GetAwardByIdQuery;
use App\Application\Queries\Award\GetAwardsQuery;
use App\Http\Resources\AwardResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AwardController extends BaseApiController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->queryBus->dispatch(new GetAwardsQuery(
            page:    (int) $request->get('page', 1),
            perPage: (int) $request->get('per_page', 15),
        ));

        return $this->success(AwardResource::collection($result));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'category'   => ['nullable', 'string', 'max:255'],
            'prize'      => ['nullable', 'string', 'max:255'],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
        ]);

        $award = $this->commandBus->dispatch(new CreateAwardCommand(
            name:      $data['name'],
            category:  $data['category'] ?? null,
            prize:     $data['prize'] ?? null,
            projectId: isset($data['project_id']) ? (int) $data['project_id'] : null,
        ));

        return $this->success(new AwardResource($award), 201);
    }

    public function show(int $id): JsonResponse
    {
        $award = $this->queryBus->dispatch(new GetAwardByIdQuery($id));

        return $this->success(new AwardResource($award));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'prize'    => ['nullable', 'string', 'max:255'],
        ]);

        $award = $this->commandBus->dispatch(new UpdateAwardCommand(
            id:       $id,
            name:     $data['name'],
            category: $data['category'] ?? null,
            prize:    $data['prize'] ?? null,
        ));

        return $this->success(new AwardResource($award));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->commandBus->dispatch(new DeleteAwardCommand($id));

        return $this->noContent();
    }

    public function assign(int $id, Request $request): JsonResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
        ]);

        $award = $this->commandBus->dispatch(new AssignAwardCommand(
            awardId:   $id,
            projectId: (int) $data['project_id'],
        ));

        return $this->success(new AwardResource($award));
    }
}
