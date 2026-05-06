import api from './axios';

export interface Team {
  id: number;
  name: string;
  max_members: number;
  leader_id?: number;
  created_at?: string;
  updated_at?: string;
}

export interface TeamPayload {
  name: string;
  max_members: number;
  leader_id?: number;
}

export interface TeamMemberPayload {
  participant_id: number;
  role?: string;
}

export const getTeams = (page = 1, search = '') =>
  api.get('/teams', { params: { page, search } });

export const createTeam = (data: TeamPayload) =>
  api.post('/teams', data);

export const getTeam = (id: number) =>
  api.get(`/teams/${id}`);

export const updateTeam = (id: number, data: TeamPayload) =>
  api.put(`/teams/${id}`, data);

export const deleteTeam = (id: number) =>
  api.delete(`/teams/${id}`);

export const getTeamMembers = (id: number) =>
  api.get(`/teams/${id}/members`);

export const addMember = (id: number, data: TeamMemberPayload) =>
  api.post(`/teams/${id}/members`, data);

export const removeMember = (teamId: number, participantId: number) =>
  api.delete(`/teams/${teamId}/members/${participantId}`);
