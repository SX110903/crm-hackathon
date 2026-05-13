<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Commands\Project\CreateProjectCommand;
use App\Application\Commands\Project\DeleteProjectCommand;
use App\Application\Commands\Project\UpdateProjectCommand;
use App\Application\CQRS\CommandBus;
use App\Application\CQRS\QueryBus;
use App\Application\Queries\Project\GetProjectByIdQuery;
use App\Application\Queries\Project\GetProjectsQuery;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ProjectController extends BaseApiController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->queryBus->dispatch(new GetProjectsQuery(
            page:    (int) $request->get('page', 1),
            perPage: (int) $request->get('per_page', 15),
            search:  $request->get('search'),
            status:  $request->get('status'),
        ));

        return $this->success(ProjectResource::collection($result));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'team_id'          => ['required', 'integer', 'exists:teams,id'],
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'category'         => ['nullable', 'string', 'max:255'],
            'technology_stack' => ['nullable', 'string'],
            'github_url'       => ['nullable', 'url', 'max:255'],
            'demo_url'         => ['nullable', 'url', 'max:255'],
            'status'           => ['nullable', 'string', 'in:In Progress,Submitted,Under Review,Evaluated,Awarded'],
        ]);

        $project = $this->commandBus->dispatch(new CreateProjectCommand(
            teamId:          (int) $data['team_id'],
            name:            $data['name'],
            description:     $data['description'] ?? null,
            category:        $data['category'] ?? null,
            technologyStack: $data['technology_stack'] ?? null,
            githubUrl:       $data['github_url'] ?? null,
            demoUrl:         $data['demo_url'] ?? null,
            status:          $data['status'] ?? 'In Progress',
        ));

        return $this->success(new ProjectResource($project), 201);
    }

    public function show(int $id): JsonResponse
    {
        $project = $this->queryBus->dispatch(new GetProjectByIdQuery($id));

        return $this->success(new ProjectResource($project));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'category'         => ['nullable', 'string', 'max:255'],
            'technology_stack' => ['nullable', 'string'],
            'github_url'       => ['nullable', 'url', 'max:255'],
            'demo_url'         => ['nullable', 'url', 'max:255'],
            'status'           => ['required', 'string', 'in:In Progress,Submitted,Under Review,Evaluated,Awarded'],
        ]);

        $project = $this->commandBus->dispatch(new UpdateProjectCommand(
            id:              $id,
            name:            $data['name'],
            description:     $data['description'] ?? null,
            category:        $data['category'] ?? null,
            technologyStack: $data['technology_stack'] ?? null,
            githubUrl:       $data['github_url'] ?? null,
            demoUrl:         $data['demo_url'] ?? null,
            status:          $data['status'],
        ));

        return $this->success(new ProjectResource($project));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->commandBus->dispatch(new DeleteProjectCommand($id));

        return $this->noContent();
    }
}
