<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Commands\Judge\CreateJudgeCommand;
use App\Application\Commands\Judge\DeleteJudgeCommand;
use App\Application\Commands\Judge\UpdateJudgeCommand;
use App\Application\CQRS\CommandBus;
use App\Application\CQRS\QueryBus;
use App\Application\Queries\Judge\GetJudgeByIdQuery;
use App\Application\Queries\Judge\GetJudgesQuery;
use App\Http\Resources\JudgeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class JudgeController extends BaseApiController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->queryBus->dispatch(new GetJudgesQuery(
            page:    (int) $request->get('page', 1),
            perPage: (int) $request->get('per_page', 15),
        ));

        return $this->success(JudgeResource::collection($result));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'          => ['required', 'string', 'max:255'],
            'last_name'           => ['required', 'string', 'max:255'],
            'email'               => ['required', 'email', 'max:255', 'unique:judges,email'],
            'company'             => ['nullable', 'string', 'max:255'],
            'expertise'           => ['nullable', 'string', 'max:255'],
            'years_of_experience' => ['nullable', 'integer', 'min:0'],
        ]);

        $judge = $this->commandBus->dispatch(new CreateJudgeCommand(
            firstName:         $data['first_name'],
            lastName:          $data['last_name'],
            email:             $data['email'],
            company:           $data['company'] ?? null,
            expertise:         $data['expertise'] ?? null,
            yearsOfExperience: isset($data['years_of_experience']) ? (int) $data['years_of_experience'] : null,
        ));

        return $this->success(new JudgeResource($judge), 201);
    }

    public function show(int $id): JsonResponse
    {
        $judge = $this->queryBus->dispatch(new GetJudgeByIdQuery($id));

        return $this->success(new JudgeResource($judge));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'first_name'          => ['required', 'string', 'max:255'],
            'last_name'           => ['required', 'string', 'max:255'],
            'email'               => ['required', 'email', 'max:255', "unique:judges,email,{$id}"],
            'company'             => ['nullable', 'string', 'max:255'],
            'expertise'           => ['nullable', 'string', 'max:255'],
            'years_of_experience' => ['nullable', 'integer', 'min:0'],
        ]);

        $judge = $this->commandBus->dispatch(new UpdateJudgeCommand(
            id:                $id,
            firstName:         $data['first_name'],
            lastName:          $data['last_name'],
            email:             $data['email'],
            company:           $data['company'] ?? null,
            expertise:         $data['expertise'] ?? null,
            yearsOfExperience: isset($data['years_of_experience']) ? (int) $data['years_of_experience'] : null,
        ));

        return $this->success(new JudgeResource($judge));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->commandBus->dispatch(new DeleteJudgeCommand($id));

        return $this->noContent();
    }
}
