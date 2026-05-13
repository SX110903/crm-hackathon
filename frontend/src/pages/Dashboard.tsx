import { Link } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  Users,
  Users2,
  Lightbulb,
  Scale,
  GraduationCap,
  ClipboardList,
  Trophy,
  Play,
  UserPlus,
  CheckCircle,
  Award,
  FileCode,
  Calendar,
  ChevronRight,
} from 'lucide-react'
import { StatCard } from '@/components/ui/StatCard'
import { Badge, getStatusBadge } from '@/components/ui/Badge'
import { dashboardApi } from '@/lib/api'
import type { DashboardStats } from '@/lib/types'

export function Dashboard() {
  const { data: stats, isLoading } = useQuery<DashboardStats>({
    queryKey: ['dashboard'],
    queryFn: () => dashboardApi.getStats().then(r => r.data.data),
    refetchInterval: 30000,
  })

  const redScore = stats?.red_team_score ?? 0
  const blueScore = stats?.blue_team_score ?? 0
  const totalScore = redScore + blueScore
  const redPercent = totalScore > 0 ? (redScore / totalScore) * 100 : 50
  const bluePercent = totalScore > 0 ? (blueScore / totalScore) * 100 : 50

  const activityIcons: Record<string, { icon: any; bg: string; color: string }> = {
    team_registered: { icon: UserPlus, bg: 'var(--primary-light)', color: 'var(--primary)' },
    evaluation_submitted: { icon: CheckCircle, bg: 'var(--success-light)', color: 'var(--success)' },
    award_assigned: { icon: Award, bg: '#fef3c7', color: '#b45309' },
    project_submitted: { icon: FileCode, bg: 'var(--warning-light)', color: 'var(--warning)' },
  }

  if (isLoading) {
    return <div style={{ padding: '40px', textAlign: 'center', color: 'var(--text-muted)' }}>Loading...</div>
  }

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Dashboard</h1>
        <Link to="/live-battle" className="btn btn-primary">
          <Play size={18} /> View Live Battle
        </Link>
      </div>

      {/* Stats Row */}
      <div className="dashboard-grid-7">
        <StatCard icon={<Users size={24} />} label="Participants" value={stats?.participants ?? 0} color="blue" linkTo="/participants" />
        <StatCard icon={<Users2 size={24} />} label="Teams" value={stats?.teams ?? 0} color="green" linkTo="/teams" />
        <StatCard icon={<Lightbulb size={24} />} label="Projects" value={stats?.projects ?? 0} color="yellow" linkTo="/projects" />
        <StatCard icon={<Scale size={24} />} label="Judges" value={stats?.judges ?? 0} color="blue" linkTo="/judges" />
        <StatCard icon={<GraduationCap size={24} />} label="Mentors" value={stats?.mentors ?? 0} color="green" linkTo="/mentors" />
        <StatCard icon={<ClipboardList size={24} />} label="Evaluations" value={stats?.evaluations ?? 0} color="yellow" linkTo="/evaluations" />
        <StatCard icon={<Trophy size={24} />} label="Awards" value={stats?.awards ?? 0} color="red" linkTo="/awards" />
      </div>

      {/* Middle Row */}
      <div className="dashboard-row">
        {/* Live Score Panel */}
        <div className="live-score-panel">
          <div className="live-score-header">
            <h3 className="live-score-title">Live Team Scores</h3>
            <span className="live-indicator">LIVE</span>
          </div>
          <div className="live-score-content">
            <div className="team-score-display">
              <div className="team-score-label" style={{ color: 'var(--red-team)' }}>Red Team</div>
              <div className="team-score-value red">{redScore}</div>
            </div>
            <div className="vs-badge">VS</div>
            <div className="team-score-display">
              <div className="team-score-label" style={{ color: 'var(--blue-team)' }}>Blue Team</div>
              <div className="team-score-value blue">{blueScore}</div>
            </div>
          </div>
          <div className="score-bars">
            <div className="score-bar-item">
              <span className="score-bar-label">Progress</span>
              <div className="score-bar-track">
                <div className="score-bar-red" style={{ width: `${redPercent}%` }} />
                <div className="score-bar-blue" style={{ width: `${bluePercent}%` }} />
              </div>
            </div>
          </div>
          <div style={{ textAlign: 'center', marginTop: '16px' }}>
            <Link to="/live-battle" className="btn btn-secondary btn-sm" style={{ backgroundColor: 'rgba(255,255,255,0.1)', borderColor: 'rgba(255,255,255,0.2)', color: '#fff' }}>
              View Full Battle <ChevronRight size={14} />
            </Link>
          </div>
        </div>

        {/* Recent Activity */}
        <div className="card">
          <div className="card-header">
            <h3 style={{ fontSize: '18px', fontWeight: 600 }}>Recent Activity</h3>
          </div>
          <div className="card-body">
            <div className="activity-feed">
              {(stats?.recent_activity ?? []).map((activity) => {
                const activityConfig = activityIcons[activity.type] || activityIcons.team_registered
                const Icon = activityConfig.icon
                return (
                  <div key={activity.id} className="activity-item">
                    <div className="activity-icon" style={{ backgroundColor: activityConfig.bg, color: activityConfig.color }}>
                      <Icon size={18} />
                    </div>
                    <div className="activity-content">
                      <p className="activity-text">{activity.message}</p>
                      <span className="activity-time">{new Date(activity.timestamp).toLocaleString()}</span>
                    </div>
                  </div>
                )
              })}
            </div>
          </div>
        </div>
      </div>

      {/* Bottom Row */}
      <div className="dashboard-row" style={{ marginTop: '24px' }}>
        {/* Top Projects */}
        <div className="card">
          <div className="card-header">
            <h3 style={{ fontSize: '18px', fontWeight: 600 }}>Top 3 Projects</h3>
          </div>
          <div className="card-body">
            {(stats?.top_projects ?? []).map((project) => (
              <div key={project.rank} className="leaderboard-item">
                <div className={`leaderboard-rank rank-${project.rank}`}>{project.rank}</div>
                <div className="leaderboard-info">
                  <div className="leaderboard-name">{project.project_name}</div>
                  <div className="leaderboard-team">{project.team_name}</div>
                </div>
                <div className="leaderboard-score">{project.score.toFixed(1)}</div>
              </div>
            ))}
          </div>
        </div>

        {/* Upcoming Events */}
        <div className="card">
          <div className="card-header">
            <h3 style={{ fontSize: '18px', fontWeight: 600 }}>Upcoming Events</h3>
          </div>
          <div className="card-body">
            {(stats?.upcoming_events ?? []).map((event) => (
              <div key={event.id} style={{ display: 'flex', alignItems: 'center', gap: '16px', padding: '12px', backgroundColor: 'var(--body-bg)', borderRadius: 'var(--radius-md)', marginBottom: '8px' }}>
                <div style={{ width: '48px', height: '48px', borderRadius: 'var(--radius-md)', backgroundColor: 'var(--primary-light)', color: 'var(--primary)', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                  <Calendar size={24} />
                </div>
                <div style={{ flex: 1 }}>
                  <div style={{ fontWeight: 600, marginBottom: '4px' }}>{event.name}</div>
                  <div style={{ fontSize: '12px', color: 'var(--text-muted)' }}>{event.start_date}</div>
                </div>
                {getStatusBadge(event.status)}
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
