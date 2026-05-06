import api from './axios';

export interface Award {
  id: number;
  name: string;
  category?: string;
  prize?: string;
  project_id?: number;
  created_at?: string;
  updated_at?: string;
}

export interface AwardPayload {
  name: string;
  category?: string;
  prize?: string;
  project_id?: number | null;
}

export const getAwards = (page = 1, search = '') =>
  api.get('/awards', { params: { page, search } });

export const createAward = (data: AwardPayload) =>
  api.post('/awards', data);

export const getAward = (id: number) =>
  api.get(`/awards/${id}`);

export const updateAward = (id: number, data: AwardPayload) =>
  api.put(`/awards/${id}`, data);

export const deleteAward = (id: number) =>
  api.delete(`/awards/${id}`);

export const assignAward = (id: number, projectId: number) =>
  api.post(`/awards/${id}/assign`, { project_id: projectId });
