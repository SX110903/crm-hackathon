<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Commands\Evaluation\CreateEvaluationCommand;
use App\Application\CQRS\CommandBus;
use App\Application\CQRS\QueryBus;
use App\Application\Queries\Evaluation\GetEvaluationByIdQuery;
use App\Application\Queries\Evaluation\GetEvaluationsQuery;
use App\Http\Resources\EvaluationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class EvaluationController extends BaseApiController
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $result = $this->queryBus->dispatch(new GetEvaluationsQuery(
            page:      (int) $request->get('page', 1),
            perPage:   (int) $request->get('per_page', 15),
            projectId: $request->has('project_id') ? (int) $request->get('project_id') : null,
        ));

        return $this->success(EvaluationResource::collection($result));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'project_id'          => ['required', 'integer', 'exists:projects,id'],
            'judge_id'            => ['required', 'integer', 'exists:judges,id'],
            'innovation_score'    => ['required', 'numeric', 'min:0', 'max:10'],
            'technical_score'     => ['required', 'numeric', 'min:0', 'max:10'],
            'presentation_score'  => ['required', 'numeric', 'min:0', 'max:10'],
            'usability_score'     => ['required', 'numeric', 'min:0', 'max:10'],
            'comments'            => ['nullable', 'string'],
        ]);

        $evaluation = $this->commandBus->dispatch(new CreateEvaluationCommand(
            projectId:         (int) $data['project_id'],
            judgeId:           (int) $data['judge_id'],
            innovationScore:   (float) $data['innovation_score'],
            technicalScore:    (float) $data['technical_score'],
            presentationScore: (float) $data['presentation_score'],
            usabilityScore:    (float) $data['usability_score'],
            comments:          $data['comments'] ?? null,
        ));

        return $this->success(new EvaluationResource($evaluation), 201);
    }

    public function show(int $id): JsonResponse
    {
        $evaluation = $this->queryBus->dispatch(new GetEvaluationByIdQuery($id));

        return $this->success(new EvaluationResource($evaluation));
    }
}
