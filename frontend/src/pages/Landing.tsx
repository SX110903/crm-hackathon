import { Link } from 'react-router-dom'
import { Calendar, Trophy, Users, Zap } from 'lucide-react'

export function Landing() {
  return (
    <div>
      {/* Hero Section */}
      <section className="landing-hero">
        <Zap size={64} style={{ color: '#2563eb', marginBottom: '24px' }} />
        <h1 className="landing-title">HackCRM</h1>
        <p className="landing-subtitle">
          Manage your Hackathons like a Pro. Create events, register teams, track scores, and celebrate winners - all in one powerful platform.
        </p>
        <div className="landing-cta">
          <Link to="/login" className="btn btn-primary btn-lg">
            Login
          </Link>
          <Link to="/register" className="btn btn-secondary btn-lg" style={{ backgroundColor: 'transparent', borderColor: 'rgba(255,255,255,0.3)', color: '#fff' }}>
            Register your Team
          </Link>
        </div>
      </section>

      {/* Features Section */}
      <section className="landing-features">
        <h2 className="landing-features-title">Everything you need to run successful hackathons</h2>
        <div className="features-grid">
          <div className="feature-card">
            <div className="feature-icon">
              <Calendar size={32} />
            </div>
            <h3 className="feature-title">Event Management</h3>
            <p className="feature-description">
              Create and manage multiple hackathon events with custom settings, dates, and participant limits.
            </p>
          </div>
          <div className="feature-card">
            <div className="feature-icon">
              <Trophy size={32} />
            </div>
            <h3 className="feature-title">Live Scoreboard</h3>
            <p className="feature-description">
              Real-time scoring with Red Team vs Blue Team competition. Track progress and celebrate achievements.
            </p>
          </div>
          <div className="feature-card">
            <div className="feature-icon">
              <Users size={32} />
            </div>
            <h3 className="feature-title">Team Tracking</h3>
            <p className="feature-description">
              Register teams, manage participants, assign mentors and judges. Keep everything organized.
            </p>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer style={{ padding: '48px 24px', backgroundColor: '#0f172a', color: '#94a3b8', textAlign: 'center' }}>
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '8px', marginBottom: '16px' }}>
          <Zap size={24} style={{ color: '#2563eb' }} />
          <span style={{ fontSize: '18px', fontWeight: 700, color: '#fff' }}>HackCRM</span>
        </div>
        <p style={{ fontSize: '14px' }}>
          2024 HackCRM. Built for hackathon organizers.
        </p>
      </footer>
    </div>
  )
}
