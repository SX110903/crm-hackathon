import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
})

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token')
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export { api }

export const authApi = {
  login: (email: string, password: string) =>
    api.post('/auth/login', { email, password }),
  logout: () => api.post('/auth/logout'),
  me: () => api.get('/auth/me'),
}

export const dashboardApi = {
  getStats: () => api.get('/dashboard'),
}

export const eventsApi = {
  getAll: (params?: { status?: string }) => api.get('/events', { params }),
  getActive: () => api.get('/events/active'),
  getById: (id: number) => api.get(`/events/${id}`),
  create: (data: unknown) => api.post('/events', data),
  update: (id: number, data: unknown) => api.put(`/events/${id}`, data),
  delete: (id: number) => api.delete(`/events/${id}`),
}

export const participantsApi = {
  getAll: (params?: { page?: number; per_page?: number; search?: string; unassigned?: boolean }) =>
    api.get('/participants', { params }),
  getById: (id: number) => api.get(`/participants/${id}`),
  create: (data: unknown) => api.post('/participants', data),
  update: (id: number, data: unknown) => api.put(`/participants/${id}`, data),
  delete: (id: number) => api.delete(`/participants/${id}`),
}

export const teamsApi = {
  getAll: (params?: { page?: number; search?: string; category?: string }) =>
    api.get('/teams', { params }),
  getById: (id: number) => api.get(`/teams/${id}`),
  create: (data: unknown) => api.post('/teams', data),
  update: (id: number, data: unknown) => api.put(`/teams/${id}`, data),
  delete: (id: number) => api.delete(`/teams/${id}`),
  getMembers: (id: number) => api.get(`/teams/${id}/members`),
  addMember: (id: number, data: unknown) => api.post(`/teams/${id}/members`, data),
  removeMember: (teamId: number, participantId: number) =>
    api.delete(`/teams/${teamId}/members/${participantId}`),
}

export const projectsApi = {
  getAll: (params?: { page?: number; search?: string; status?: string }) =>
    api.get('/projects', { params }),
  getById: (id: number) => api.get(`/projects/${id}`),
  create: (data: unknown) => api.post('/projects', data),
  update: (id: number, data: unknown) => api.put(`/projects/${id}`, data),
  delete: (id: number) => api.delete(`/projects/${id}`),
}

export const judgesApi = {
  getAll: (params?: { page?: number }) => api.get('/judges', { params }),
  getById: (id: number) => api.get(`/judges/${id}`),
  create: (data: unknown) => api.post('/judges', data),
  update: (id: number, data: unknown) => api.put(`/judges/${id}`, data),
  delete: (id: number) => api.delete(`/judges/${id}`),
}

export const mentorsApi = {
  getAll: (params?: { page?: number }) => api.get('/mentors', { params }),
  getById: (id: number) => api.get(`/mentors/${id}`),
  create: (data: unknown) => api.post('/mentors', data),
  update: (id: number, data: unknown) => api.put(`/mentors/${id}`, data),
  delete: (id: number) => api.delete(`/mentors/${id}`),
}

export const evaluationsApi = {
  getAll: (params?: { page?: number; project_id?: number }) =>
    api.get('/evaluations', { params }),
  getById: (id: number) => api.get(`/evaluations/${id}`),
  create: (data: unknown) => api.post('/evaluations', data),
}

export const awardsApi = {
  getAll: (params?: { page?: number }) => api.get('/awards', { params }),
  getById: (id: number) => api.get(`/awards/${id}`),
  create: (data: unknown) => api.post('/awards', data),
  update: (id: number, data: unknown) => api.put(`/awards/${id}`, data),
  delete: (id: number) => api.delete(`/awards/${id}`),
  assign: (id: number, project_id: number) =>
    api.post(`/awards/${id}/assign`, { project_id }),
}

export const registrationApi = {
  register: (data: {
    event_id: number
    team_name: string
    category: 'red' | 'blue'
    members: { name: string; email: string; role: string }[]
  }) => api.post('/register', data),
}

export default api
