import api from './axios';

export interface Judge {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  company?: string;
  expertise?: string;
  years_of_experience?: number;
  created_at?: string;
  updated_at?: string;
}

export interface JudgePayload {
  first_name: string;
  last_name: string;
  email: string;
  company?: string;
  expertise?: string;
  years_of_experience?: number;
}

export const getJudges = (page = 1, search = '') =>
  api.get('/judges', { params: { page, search } });

export const createJudge = (data: JudgePayload) =>
  api.post('/judges', data);

export const getJudge = (id: number) =>
  api.get(`/judges/${id}`);

export const updateJudge = (id: number, data: JudgePayload) =>
  api.put(`/judges/${id}`, data);

export const deleteJudge = (id: number) =>
  api.delete(`/judges/${id}`);
