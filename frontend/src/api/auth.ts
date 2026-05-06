import api from './axios';

export interface LoginResponse {
  data: {
    token: string;
    user: {
      id: number;
      name: string;
      email: string;
      role: string;
    };
  };
}

export const login = (email: string, password: string) =>
  api.post<LoginResponse>('/auth/login', { email, password });

export const logout = () =>
  api.post('/auth/logout');

export const me = () =>
  api.get('/auth/me');
