import { Users } from 'lucide-react'
import { useQuery } from '@tanstack/react-query'
import { teamsApi } from '@/lib/api'
import type { Team } from '@/lib/types'

export function BlueTeam() {
  const { data: blueTeams = [], isLoading } = useQuery<Team[]>({
    queryKey: ['teams', 'blue'],
    queryFn: () => teamsApi.getAll({ category: 'blue' }).then(r => r.data.data),
  })

  const sorted = [...blueTeams].sort((a, b) => (b.score ?? 0) - (a.score ?? 0))
  const totalScore = sorted.reduce((sum, t) => sum + (t.score ?? 0), 0)

  return (
    <div>
      <div className="team-banner blue">
        <div className="team-banner-content">
          <h1 className="team-banner-title">Blue Team</h1>
          <div className="team-banner-stats">
            <div className="team-banner-stat">
              <div className="team-banner-stat-value">{totalScore.toFixed(1)}</div>
              <div className="team-banner-stat-label">Total Score</div>
            </div>
            <div className="team-banner-stat">
              <div className="team-banner-stat-value">{sorted.length}</div>
              <div className="team-banner-stat-label">Teams</div>
            </div>
          </div>
        </div>
      </div>

      <h2 style={{ fontSize: '20px', fontWeight: 600, marginBottom: '16px' }}>Teams</h2>
      {isLoading ? (
        <div style={{ color: 'var(--text-muted)' }}>Loading...</div>
      ) : (
        <div className="cards-grid">
          {sorted.map((team) => (
            <div key={team.id} className="team-card team-blue">
              <div className="team-card-header">
                <div>
                  <div className="team-card-name">{team.name}</div>
                  <div className="team-card-meta">
                    <Users size={14} style={{ verticalAlign: 'middle', marginRight: '4px' }} />
                    Max {team.max_members} members
                  </div>
                </div>
                <div className="team-card-score score-blue">{(team.score ?? 0).toFixed(1)}</div>
              </div>
            </div>
          ))}
        </div>
      )}

      <h2 style={{ fontSize: '20px', fontWeight: 600, marginTop: '32px', marginBottom: '16px' }}>Scoreboard</h2>
      <div className="table-container">
        <table className="table">
          <thead>
            <tr>
              <th>Rank</th>
              <th>Team Name</th>
              <th>Total Score</th>
            </tr>
          </thead>
          <tbody>
            {sorted.map((team, index) => (
              <tr key={team.id} className="table-row-blue">
                <td>
                  <div style={{ width: '28px', height: '28px', borderRadius: '50%', backgroundColor: index === 0 ? '#fef3c7' : index === 1 ? '#e2e8f0' : index === 2 ? '#fed7aa' : 'transparent', color: index === 0 ? '#b45309' : index === 1 ? '#475569' : index === 2 ? '#c2410c' : 'var(--text-primary)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 600 }}>
                    {index + 1}
                  </div>
                </td>
                <td style={{ fontWeight: 500 }}>{team.name}</td>
                <td><span style={{ fontWeight: 700, color: 'var(--blue-team)' }}>{(team.score ?? 0).toFixed(1)}</span></td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  )
}
