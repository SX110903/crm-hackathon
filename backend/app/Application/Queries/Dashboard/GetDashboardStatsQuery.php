<?php

declare(strict_types=1);

namespace App\Application\Queries\Dashboard;

use App\Application\CQRS\Contracts\QueryInterface;

final class GetDashboardStatsQuery implements QueryInterface
{
    public function __construct() {}
}
