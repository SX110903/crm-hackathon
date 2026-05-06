import api from './axios';

export interface Project {
  id: number;
  team_id: number;
  name: string;
  description?: string;
  category?: string;
  technology_stack?: string;
  github_url?: string;
  demo_url?: string;
  status?: string;
  created_at?: string;
  updated_at?: string;
}

export interface ProjectPayload {
  team_id: number;
  name: string;
  description?: string;
  category?: string;
  technology_stack?: string;
  github_url?: string;
  demo_url?: string;
  status?: string;
}

export const getProjects = (page = 1, search = '') =>
  api.get('/projects', { params: { page, search } });

export const createProject = (data: ProjectPayload) =>
  api.post('/projects', data);

export const getProject = (id: number) =>
  api.get(`/projects/${id}`);

export const updateProject = (id: number, data: ProjectPayload) =>
  api.put(`/projects/${id}`, data);

export const deleteProject = (id: number) =>
  api.delete(`/projects/${id}`);
