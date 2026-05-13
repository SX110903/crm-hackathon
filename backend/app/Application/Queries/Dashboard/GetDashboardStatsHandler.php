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
        $counts = [
            'participants' => DB::table('participants')->count(),
            'teams'        => DB::table('teams')->count(),
            'projects'     => DB::table('projects')->count(),
            'judges'       => DB::table('judges')->count(),
            'mentors'      => DB::table('mentors')->count(),
            'evaluations'  => DB::table('evaluations')->count(),
            'awards'       => DB::table('awards')->count(),
        ];

        $redScore = DB::table('evaluations')
            ->join('projects', 'evaluations.project_id', '=', 'projects.id')
            ->join('teams', 'projects.team_id', '=', 'teams.id')
            ->where('teams.category', 'red')
            ->avg(DB::raw('(evaluations.innovation_score + evaluations.technical_score + evaluations.presentation_score + evaluations.usability_score) / 4'));

        $blueScore = DB::table('evaluations')
            ->join('projects', 'evaluations.project_id', '=', 'projects.id')
            ->join('teams', 'projects.team_id', '=', 'teams.id')
            ->where('teams.category', 'blue')
            ->avg(DB::raw('(evaluations.innovation_score + evaluations.technical_score + evaluations.presentation_score + evaluations.usability_score) / 4'));

        $topProjects = DB::table('evaluations')
            ->join('projects', 'evaluations.project_id', '=', 'projects.id')
            ->join('teams', 'projects.team_id', '=', 'teams.id')
            ->select(
                'projects.id',
                'projects.name as project_name',
                'teams.name as team_name',
                DB::raw('ROUND(AVG((evaluations.innovation_score + evaluations.technical_score + evaluations.presentation_score + evaluations.usability_score) / 4), 2) as avg_score')
            )
            ->groupBy('projects.id', 'projects.name', 'teams.name')
            ->orderByDesc('avg_score')
            ->limit(3)
            ->get()
            ->values()
            ->map(fn($p, $i) => [
                'rank'         => $i + 1,
                'project_name' => $p->project_name,
                'team_name'    => $p->team_name,
                'score'        => (float) $p->avg_score,
            ])->toArray();

        $upcomingEvents = DB::table('events')
            ->whereIn('status', ['upcoming', 'active'])
            ->orderBy('start_date')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'id'         => $e->id,
                'name'       => $e->name,
                'start_date' => $e->start_date,
                'status'     => $e->status,
            ])->toArray();

        $recentActivity = DB::table('evaluations')
            ->join('projects', 'evaluations.project_id', '=', 'projects.id')
            ->join('judges', 'evaluations.judge_id', '=', 'judges.id')
            ->select(
                'evaluations.id',
                'evaluations.created_at',
                'projects.name as project_name',
                DB::raw("CONCAT(judges.first_name, ' ', judges.last_name) as judge_name"),
                DB::raw('ROUND((evaluations.innovation_score + evaluations.technical_score + evaluations.presentation_score + evaluations.usability_score) / 4, 1) as avg_score')
            )
            ->orderByDesc('evaluations.created_at')
            ->limit(5)
            ->get()
            ->map(fn($e) => [
                'id'        => $e->id,
                'type'      => 'evaluation_submitted',
                'message'   => "Judge {$e->judge_name} scored {$e->project_name}: {$e->avg_score}/10",
                'timestamp' => $e->created_at,
            ])->toArray();

        return array_merge($counts, [
            'red_team_score'  => round((float) ($redScore ?? 0), 2),
            'blue_team_score' => round((float) ($blueScore ?? 0), 2),
            'top_projects'    => $topProjects,
            'upcoming_events' => $upcomingEvents,
            'recent_activity' => $recentActivity,
        ]);
    }
}
