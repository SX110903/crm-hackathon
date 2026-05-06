import React from 'react';
import { useQuery } from '@tanstack/react-query';
import { getDashboardStats, DashboardStats } from '../api/dashboard';

interface StatCard {
  label: string;
  key: keyof DashboardStats;
  icon: string;
  color: string;
  path: string;
}

const statCards: StatCard[] = [
  { label: 'Participants', key: 'participants', icon: '👥', color: '#3b82f6', path: '/participants' },
  { label: 'Teams', key: 'teams', icon: '🏆', color: '#8b5cf6', path: '/teams' },
  { label: 'Projects', key: 'projects', icon: '💡', color: '#f59e0b', path: '/projects' },
  { label: 'Judges', key: 'judges', icon: '⚖️', color: '#10b981', path: '/judges' },
  { label: 'Mentors', key: 'mentors', icon: '🎓', color: '#ef4444', path: '/mentors' },
  { label: 'Evaluations', key: 'evaluations', icon: '📋', color: '#06b6d4', path: '/evaluations' },
  { label: 'Awards', key: 'awards', icon: '🥇', color: '#f97316', path: '/awards' },
];

const Dashboard: React.FC = () => {
  const { data, isLoading, isError } = useQuery({
    queryKey: ['dashboard'],
    queryFn: () => getDashboardStats(),
  });

  const stats: DashboardStats = data?.data?.data ?? {
    participants: 0,
    teams: 0,
    projects: 0,
    judges: 0,
    mentors: 0,
    evaluations: 0,
    awards: 0,
  };

  if (isLoading) {
    return (
      <div className="loading-container">
        <div className="spinner"></div>
        <p>Loading dashboard...</p>
      </div>
    );
  }

  if (isError) {
    return (
      <div className="alert alert-error">
        Failed to load dashboard statistics. Please try again.
      </div>
    );
  }

  return (
    <div className="dashboard">
      <div className="page-header">
        <h1 className="page-heading">Dashboard</h1>
        <p className="page-subheading">Welcome to the CRM Hackathon Management System</p>
      </div>

      <div className="stats-grid">
        {statCards.map(card => (
          <a key={card.key} href={card.path} className="stat-card" style={{ borderTop: `4px solid ${card.color}` }}>
            <div className="stat-card-header">
              <span className="stat-icon">{card.icon}</span>
              <span className="stat-label">{card.label}</span>
            </div>
            <div className="stat-value" style={{ color: card.color }}>
              {stats[card.key] ?? 0}
            </div>
            <div className="stat-footer">
              View all &rarr;
            </div>
          </a>
        ))}
      </div>
    </div>
  );
};

export default Dashboard;
