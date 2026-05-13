import { useState } from 'react'
import { Link, useLocation, useNavigate, Outlet } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import {
  Zap,
  LayoutDashboard,
  Calendar,
  Users,
  CircleDot,
  Lightbulb,
  Scale,
  GraduationCap,
  ClipboardList,
  Trophy,
  UserPlus,
  Settings,
  LogOut,
  Menu,
  X,
  ChevronDown,
} from 'lucide-react'
import { useAuth } from '@/lib/auth'
import { eventsApi } from '@/lib/api'
import type { Event } from '@/lib/types'

const navItems = [
  { path: '/dashboard', label: 'Dashboard', icon: LayoutDashboard },
  { path: '/events', label: 'Events', icon: Calendar },
  { path: '/participants', label: 'Participants', icon: Users },
  { path: '/red-team', label: 'Red Team', icon: CircleDot, color: '#dc2626' },
  { path: '/blue-team', label: 'Blue Team', icon: CircleDot, color: '#2563eb' },
  { path: '/projects', label: 'Projects', icon: Lightbulb },
  { path: '/judges', label: 'Judges', icon: Scale },
  { path: '/mentors', label: 'Mentors', icon: GraduationCap },
  { path: '/evaluations', label: 'Evaluations', icon: ClipboardList },
  { path: '/awards', label: 'Awards', icon: Trophy },
  { path: '/team-assignment', label: 'Team Assignment', icon: UserPlus },
  { path: '/settings', label: 'Settings', icon: Settings },
]

export function Layout() {
  const location = useLocation()
  const navigate = useNavigate()
  const { user, logout } = useAuth()
  const [sidebarOpen, setSidebarOpen] = useState(false)
  const [selectedEvent, setSelectedEvent] = useState('')

  const { data: events = [] } = useQuery<Event[]>({
    queryKey: ['events'],
    queryFn: () => eventsApi.getAll().then(r => {
      const list: Event[] = r.data.data
      if (list.length > 0 && !selectedEvent) setSelectedEvent(String(list[0].id))
      return list
    }),
  })

  const handleLogout = () => {
    logout()
    navigate('/login')
  }

  return (
    <div className="admin-layout">
      <aside className={`sidebar ${sidebarOpen ? 'open' : ''}`}>
        <div className="sidebar-header">
          <Link to="/dashboard" className="sidebar-logo">
            <Zap size={28} />
            HackCRM
          </Link>
        </div>
        <nav className="sidebar-nav">
          {navItems.map((item) => (
            <Link
              key={item.path}
              to={item.path}
              className={`sidebar-nav-item ${location.pathname === item.path ? 'active' : ''}`}
              onClick={() => setSidebarOpen(false)}
            >
              <item.icon size={20} style={item.color ? { color: item.color } : undefined} />
              {item.label}
            </Link>
          ))}
        </nav>
      </aside>

      {sidebarOpen && (
        <div
          style={{ position: 'fixed', inset: 0, backgroundColor: 'rgba(0,0,0,0.5)', zIndex: 99 }}
          onClick={() => setSidebarOpen(false)}
        />
      )}

      <div className="main-content">
        <header className="top-header">
          <div className="top-header-left">
            <button className="hamburger-btn" onClick={() => setSidebarOpen(!sidebarOpen)}>
              {sidebarOpen ? <X size={24} /> : <Menu size={24} />}
            </button>
            <div style={{ position: 'relative' }}>
              <select
                className="event-selector"
                value={selectedEvent}
                onChange={(e) => setSelectedEvent(e.target.value)}
              >
                {events.map((event) => (
                  <option key={event.id} value={event.id}>{event.name}</option>
                ))}
              </select>
              <ChevronDown size={16} style={{ position: 'absolute', right: '12px', top: '50%', transform: 'translateY(-50%)', pointerEvents: 'none', color: 'var(--text-muted)' }} />
            </div>
          </div>
          <div className="top-header-right">
            <div className="user-menu">
              <div className="user-avatar">{user?.name?.charAt(0) || 'A'}</div>
              <span style={{ fontWeight: 500 }}>{user?.name || 'Admin'}</span>
            </div>
            <button className="btn btn-ghost btn-icon" onClick={handleLogout} title="Logout">
              <LogOut size={20} />
            </button>
          </div>
        </header>

        <main className="page-content">
          <Outlet />
        </main>
      </div>
    </div>
  )
}
