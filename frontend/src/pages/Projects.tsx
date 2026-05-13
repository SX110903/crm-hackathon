import { useState } from 'react'
import { Plus, Edit, Trash2 } from 'lucide-react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Modal } from '@/components/ui/Modal'
import { Badge, getStatusBadge } from '@/components/ui/Badge'
import { projectsApi, teamsApi } from '@/lib/api'
import type { Project, Team } from '@/lib/types'

export function Projects() {
  const queryClient = useQueryClient()
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingProject, setEditingProject] = useState<Project | null>(null)

  const [formData, setFormData] = useState({
    name: '',
    team_id: '' as string | number,
    description: '',
    technology_stack: '',
    status: 'In Progress' as Project['status'],
  })

  const { data: projects = [], isLoading } = useQuery<Project[]>({
    queryKey: ['projects'],
    queryFn: () => projectsApi.getAll().then(r => r.data.data),
  })

  const { data: teams = [] } = useQuery<Team[]>({
    queryKey: ['teams'],
    queryFn: () => teamsApi.getAll().then(r => r.data.data),
  })

  const createMutation = useMutation({
    mutationFn: (data: unknown) => projectsApi.create(data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['projects'] }),
  })

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: unknown }) => projectsApi.update(id, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['projects'] }),
  })

  const deleteMutation = useMutation({
    mutationFn: (id: number) => projectsApi.delete(id),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['projects'] }),
  })

  const openCreateModal = () => {
    setEditingProject(null)
    setFormData({ name: '', team_id: '', description: '', technology_stack: '', status: 'In Progress' })
    setIsModalOpen(true)
  }

  const openEditModal = (p: Project) => {
    setEditingProject(p)
    setFormData({
      name: p.name,
      team_id: p.team_id,
      description: p.description ?? '',
      technology_stack: p.technology_stack ?? '',
      status: p.status,
    })
    setIsModalOpen(true)
  }

  const handleSubmit = () => {
    const payload = { ...formData, team_id: formData.team_id || null }
    if (editingProject) {
      updateMutation.mutate({ id: editingProject.id, data: payload })
    } else {
      createMutation.mutate(payload)
    }
    setIsModalOpen(false)
  }

  const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this project?')) {
      deleteMutation.mutate(id)
    }
  }

  const getTeamName = (team_id: number) => teams.find(t => t.id === team_id)?.name ?? '-'

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Projects</h1>
        <button className="btn btn-primary" onClick={openCreateModal}>
          <Plus size={18} /> Add Project
        </button>
      </div>

      <div className="table-container">
        <table className="table">
          <thead>
            <tr>
              <th>Project Name</th>
              <th>Team</th>
              <th>Status</th>
              <th>Tech Stack</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={5} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>Loading...</td></tr>
            ) : projects.map((project) => (
              <tr key={project.id}>
                <td style={{ fontWeight: 500 }}>{project.name}</td>
                <td>{getTeamName(project.team_id)}</td>
                <td>{getStatusBadge(project.status)}</td>
                <td>
                  {project.technology_stack ? (
                    <span style={{ fontSize: '12px', padding: '2px 6px', backgroundColor: 'var(--body-bg)', borderRadius: '4px' }}>
                      {project.technology_stack}
                    </span>
                  ) : '-'}
                </td>
                <td>
                  <div className="action-buttons">
                    <button className="btn btn-ghost btn-sm" onClick={() => openEditModal(project)}>
                      <Edit size={14} />
                    </button>
                    <button className="btn btn-ghost btn-sm" style={{ color: 'var(--error)' }} onClick={() => handleDelete(project.id)}>
                      <Trash2 size={14} />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title={editingProject ? 'Edit Project' : 'Add Project'}
        footer={
          <>
            <button className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>Cancel</button>
            <button className="btn btn-primary" onClick={handleSubmit}>
              {editingProject ? 'Save Changes' : 'Add Project'}
            </button>
          </>
        }
      >
        <div className="form-group">
          <label className="form-label">Project Name</label>
          <input type="text" className="form-input" value={formData.name}
            onChange={(e) => setFormData({ ...formData, name: e.target.value })} placeholder="Enter project name" />
        </div>
        <div className="form-group">
          <label className="form-label">Team</label>
          <select className="form-select w-full" value={formData.team_id}
            onChange={(e) => setFormData({ ...formData, team_id: e.target.value })}>
            <option value="">Select team</option>
            {teams.map((team) => (
              <option key={team.id} value={team.id}>{team.name}</option>
            ))}
          </select>
        </div>
        <div className="form-group">
          <label className="form-label">Description</label>
          <textarea className="form-textarea" value={formData.description}
            onChange={(e) => setFormData({ ...formData, description: e.target.value })} placeholder="Project description" />
        </div>
        <div className="form-group">
          <label className="form-label">Tech Stack</label>
          <input type="text" className="form-input" value={formData.technology_stack}
            onChange={(e) => setFormData({ ...formData, technology_stack: e.target.value })} placeholder="React, Node.js, MongoDB" />
        </div>
        <div className="form-group">
          <label className="form-label">Status</label>
          <select className="form-select w-full" value={formData.status}
            onChange={(e) => setFormData({ ...formData, status: e.target.value as any })}>
            <option value="In Progress">In Progress</option>
            <option value="Submitted">Submitted</option>
            <option value="Under Review">Under Review</option>
            <option value="Evaluated">Evaluated</option>
            <option value="Awarded">Awarded</option>
          </select>
        </div>
      </Modal>
    </div>
  )
}
