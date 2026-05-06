<?php

declare(strict_types=1);

namespace App\Application\Queries\Dashboard;

use App\Application\CQRS\Contracts\QueryHandlerInterface;
use App\Application\CQRS\Contracts\QueryInterface;
use Illuminate\Support\Facades\DB;

final class GetDashboardStatsHandler implements QueryHandlerInterface
{
    public function handle(QueryInterface $query): array
    {
        /** @var GetDashboardStatsQuery $query */
        return [
            'participants' => DB::table('participants')->count(),
            'teams'        => DB::table('teams')->count(),
            'projects'     => DB::table('projects')->count(),
            'evaluations'  => DB::table('evaluations')->count(),
        ];
    }
}
