<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\CQRS\QueryBus;
use App\Application\Queries\Dashboard\GetDashboardStatsQuery;
use Illuminate\Http\JsonResponse;

final class DashboardController extends BaseApiController
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {}

    public function index(): JsonResponse
    {
        $stats = $this->queryBus->dispatch(new GetDashboardStatsQuery());

        return $this->success($stats);
    }
}
