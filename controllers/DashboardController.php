<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/DashboardModel.php';

class DashboardController extends BaseController
{
    private DashboardModel $dashboardModel;

    public function __construct()
    {
        parent::__construct();
        $this->dashboardModel = new DashboardModel();
    }

    public function index(?int $id): void
    {
        $stats        = $this->dashboardModel->getStats();
        $rankings     = $this->dashboardModel->getProjectRankings(5);
        $recentEvents = $this->dashboardModel->getRecentEvents(8);
        $topTeams     = $this->dashboardModel->getTopTeams(5);
        $statusDist   = $this->dashboardModel->getProjectStatusDistribution();

        $this->render('dashboard/index', compact(
            'stats', 'rankings', 'recentEvents', 'topTeams', 'statusDist'
        ));
    }
}
