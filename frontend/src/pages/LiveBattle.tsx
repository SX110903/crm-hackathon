import { useState, useEffect } from 'react'
import { Link } from 'react-router-dom'
import { ArrowLeft } from 'lucide-react'
import { useQuery } from '@tanstack/react-query'
import { dashboardApi } from '@/lib/api'
import type { DashboardStats } from '@/lib/types'

export function LiveBattle() {
  const [lastUpdated, setLastUpdated] = useState(new Date())
  const [timeRemaining, setTimeRemaining] = useState('23:45:32')

  const { data: stats } = useQuery<DashboardStats>({
    queryKey: ['dashboard'],
    queryFn: () => dashboardApi.getStats().then(r => r.data.data),
    refetchInterval: 10000,
  })

  const redScore = stats?.red_team_score ?? 0
  const blueScore = stats?.blue_team_score ?? 0

  useEffect(() => {
    const interval = setInterval(() => setLastUpdated(new Date()), 10000)
    return () => clearInterval(interval)
  }, [])

  useEffect(() => {
    const interval = setInterval(() => {
      const parts = timeRemaining.split(':').map(Number)
      let [h, m, s] = parts
      if (s > 0) s--
      else if (m > 0) { m--; s = 59 }
      else if (h > 0) { h--; m = 59; s = 59 }
      setTimeRemaining(`${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`)
    }, 1000)
    return () => clearInterval(interval)
  }, [timeRemaining])

  const currentEvent = (stats?.upcoming_events ?? []).find(e => e.status === 'active')

  return (
    <div className="live-battle">
      <div className="live-battle-header">
        <Link to="/dashboard" className="btn btn-ghost" style={{ color: '#fff' }}>
          <ArrowLeft size={20} /> Back to Dashboard
        </Link>
        <h1 className="live-battle-title">{currentEvent?.name || 'Live Battle'}</h1>
        <div style={{ display: 'flex', alignItems: 'center', gap: '16px' }}>
          <span className="live-indicator">LIVE</span>
          <span style={{ fontSize: '12px', color: 'var(--text-muted)' }}>
            Last updated: {lastUpdated.toLocaleTimeString()}
          </span>
        </div>
      </div>

      <div className="live-battle-content">
        {/* Red Team Panel */}
        <div className="battle-team-panel red">
          <div style={{ display: 'flex', alignItems: 'center', gap: '16px', marginBottom: '16px' }}>
            <div style={{ width: '64px', height: '64px', borderRadius: '50%', backgroundColor: 'var(--red-team)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '24px', fontWeight: 700 }}>R</div>
            <h2 className="battle-team-name" style={{ color: 'var(--red-team)' }}>Red Team</h2>
          </div>
          <div className="battle-team-score" style={{ color: 'var(--red-team)' }}>{redScore}</div>
        </div>

        {/* Center Divider */}
        <div className="battle-divider">
          <div className="battle-vs">VS</div>
          <div className="battle-timer">
            <div className="battle-timer-value">{timeRemaining}</div>
            <div className="battle-timer-label">Time Remaining</div>
          </div>
          <div style={{ textAlign: 'center' }}>
            <div style={{ fontSize: '14px', color: 'var(--text-muted)', marginBottom: '4px' }}>Current Event</div>
            <div style={{ fontWeight: 600 }}>{currentEvent?.name || 'N/A'}</div>
          </div>
        </div>

        {/* Blue Team Panel */}
        <div className="battle-team-panel blue">
          <div style={{ display: 'flex', alignItems: 'center', gap: '16px', marginBottom: '16px' }}>
            <div style={{ width: '64px', height: '64px', borderRadius: '50%', backgroundColor: 'var(--blue-team)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '24px', fontWeight: 700 }}>B</div>
            <h2 className="battle-team-name" style={{ color: 'var(--blue-team)' }}>Blue Team</h2>
          </div>
          <div className="battle-team-score" style={{ color: 'var(--blue-team)' }}>{blueScore}</div>
        </div>
      </div>
    </div>
  )
}
