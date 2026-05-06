import api from './axios';

export interface Participant {
  id: number;
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  university?: string;
  major?: string;
  year_of_study?: number;
  created_at?: string;
  updated_at?: string;
}

export interface ParticipantPayload {
  first_name: string;
  last_name: string;
  email: string;
  phone?: string;
  university?: string;
  major?: string;
  year_of_study?: number;
}

export const getParticipants = (page = 1, search = '') =>
  api.get('/participants', { params: { page, search } });

export const createParticipant = (data: ParticipantPayload) =>
  api.post('/participants', data);

export const getParticipant = (id: number) =>
  api.get(`/participants/${id}`);

export const updateParticipant = (id: number, data: ParticipantPayload) =>
  api.put(`/participants/${id}`, data);

export const deleteParticipant = (id: number) =>
  api.delete(`/participants/${id}`);
