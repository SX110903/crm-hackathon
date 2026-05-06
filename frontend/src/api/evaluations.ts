import api from './axios';

export interface Evaluation {
  id: number;
  project_id: number;
  judge_id: number;
  innovation_score: number;
  technical_score: number;
  presentation_score: number;
  usability_score: number;
  comments?: string;
  created_at?: string;
  updated_at?: string;
}

export interface EvaluationPayload {
  project_id: number;
  judge_id: number;
  innovation_score: number;
  technical_score: number;
  presentation_score: number;
  usability_score: number;
  comments?: string;
}

export const getEvaluations = (page = 1, search = '') =>
  api.get('/evaluations', { params: { page, search } });

export const createEvaluation = (data: EvaluationPayload) =>
  api.post('/evaluations', data);

export const getEvaluation = (id: number) =>
  api.get(`/evaluations/${id}`);

export const updateEvaluation = (id: number, data: EvaluationPayload) =>
  api.put(`/evaluations/${id}`, data);

export const deleteEvaluation = (id: number) =>
  api.delete(`/evaluations/${id}`);
