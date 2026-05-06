import api from './axios';

export interface Mentor {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  company?: string;
  specialization?: string;
  available_slots?: number;
  created_at?: string;
  updated_at?: string;
}

export interface MentorPayload {
  first_name: string;
  last_name: string;
  email: string;
  company?: string;
  specialization?: string;
  available_slots?: number;
}

export const getMentors = (page = 1, search = '') =>
  api.get('/mentors', { params: { page, search } });

export const createMentor = (data: MentorPayload) =>
  api.post('/mentors', data);

export const getMentor = (id: number) =>
  api.get(`/mentors/${id}`);

export const updateMentor = (id: number, data: MentorPayload) =>
  api.put(`/mentors/${id}`, data);

export const deleteMentor = (id: number) =>
  api.delete(`/mentors/${id}`);
