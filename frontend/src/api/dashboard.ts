import api from './axios';

export interface DashboardStats {
  participants: number;
  teams: number;
  projects: number;
  judges: number;
  mentors: number;
  evaluations: number;
  awards: number;
}

export const getDashboardStats = () =>
  api.get<{ data: DashboardStats }>('/dashboard');
