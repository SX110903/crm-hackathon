import React, { useState } from 'react';
import { Link, Outlet, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../contexts/AuthContext';
import { logout as apiLogout } from '../api/auth';

const navItems = [
  { path: '/dashboard', label: 'Dashboard', icon: '📊' },
  { path: '/participants', label: 'Participants', icon: '👥' },
  { path: '/teams', label: 'Teams', icon: '🏆' },
  { path: '/projects', label: 'Projects', icon: '💡' },
  { path: '/judges', label: 'Judges', icon: '⚖️' },
  { path: '/mentors', label: 'Mentors', icon: '🎓' },
  { path: '/evaluations', label: 'Evaluations', icon: '📋' },
  { path: '/awards', label: 'Awards', icon: '🥇' },
];

const Layout: React.FC = () => {
  const { user, logout } = useAuth();
  const location = useLocation();
  const navigate = useNavigate();
  const [sidebarOpen, setSidebarOpen] = useState(true);

  const handleLogout = async () => {
    try {
      await apiLogout();
    } catch {
      // ignore
    }
    logout();
    navigate('/login');
  };

  return (
    <div className="layout">
      <aside className={`sidebar ${sidebarOpen ? 'sidebar-open' : 'sidebar-closed'}`}>
        <div className="sidebar-header">
          <div className="sidebar-logo">
            <span className="logo-icon">🚀</span>
            {sidebarOpen && <span className="logo-text">CRM Hackathon</span>}
          </div>
          <button
            className="sidebar-toggle"
            onClick={() => setSidebarOpen(!sidebarOpen)}
            aria-label="Toggle sidebar"
          >
            {sidebarOpen ? '◀' : '▶'}
          </button>
        </div>
        <nav className="sidebar-nav">
          {navItems.map(item => (
            <Link
              key={item.path}
              to={item.path}
              className={`nav-link ${location.pathname.startsWith(item.path) ? 'nav-link-active' : ''}`}
              title={!sidebarOpen ? item.label : undefined}
            >
              <span className="nav-icon">{item.icon}</span>
              {sidebarOpen && <span className="nav-label">{item.label}</span>}
            </Link>
          ))}
        </nav>
      </aside>

      <div className="main-wrapper">
        <header className="top-header">
          <div className="header-left">
            <h2 className="page-title">
              {navItems.find(i => location.pathname.startsWith(i.path))?.label ?? 'CRM Hackathon'}
            </h2>
          </div>
          <div className="header-right">
            <div className="user-info">
              <div className="user-avatar">
                {user?.name?.charAt(0).toUpperCase() ?? 'U'}
              </div>
              <div className="user-details">
                <span className="user-name">{user?.name}</span>
                <span className="user-role">{user?.role}</span>
              </div>
            </div>
            <button className="btn btn-outline logout-btn" onClick={handleLogout}>
              Logout
            </button>
          </div>
        </header>

        <main className="main-content">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default Layout;
