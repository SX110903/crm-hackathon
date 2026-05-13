import { useState } from 'react'
import { Plus, Edit, Trash2 } from 'lucide-react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Modal } from '@/components/ui/Modal'
import { Badge, getScoreBadge } from '@/components/ui/Badge'
import { teamsApi, eventsApi } from '@/lib/api'
import type { Team, Event } from '@/lib/types'

export function Teams() {
  const queryClient = useQueryClient()
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingTeam, setEditingTeam] = useState<Team | null>(null)

  const [formData, setFormData] = useState({
    name: '',
    category: 'general' as 'red' | 'blue' | 'general',
    event_id: '' as string | number,
  })

  const { data: teams = [], isLoading } = useQuery<Team[]>({
    queryKey: ['teams'],
    queryFn: () => teamsApi.getAll().then(r => r.data.data),
  })

  const { data: events = [] } = useQuery<Event[]>({
    queryKey: ['events'],
    queryFn: () => eventsApi.getAll().then(r => r.data.data),
  })

  const createMutation = useMutation({
    mutationFn: (data: unknown) => teamsApi.create(data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['teams'] }),
  })

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: unknown }) => teamsApi.update(id, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['teams'] }),
  })

  const deleteMutation = useMutation({
    mutationFn: (id: number) => teamsApi.delete(id),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['teams'] }),
  })

  const openCreateModal = () => {
    setEditingTeam(null)
    setFormData({ name: '', category: 'general', event_id: '' })
    setIsModalOpen(true)
  }

  const openEditModal = (team: Team) => {
    setEditingTeam(team)
    setFormData({ name: team.name, category: team.category, event_id: team.event_id ?? '' })
    setIsModalOpen(true)
  }

  const handleSubmit = () => {
    const payload = { ...formData, event_id: formData.event_id || null }
    if (editingTeam) {
      updateMutation.mutate({ id: editingTeam.id, data: payload })
    } else {
      createMutation.mutate(payload)
    }
    setIsModalOpen(false)
  }

  const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this team?')) {
      deleteMutation.mutate(id)
    }
  }

  const getCategoryBadge = (category: string) => {
    switch (category) {
      case 'red': return <Badge variant="error">Red Team</Badge>
      case 'blue': return <Badge variant="primary">Blue Team</Badge>
      default: return <Badge variant="gray">General</Badge>
    }
  }

  const getEventName = (event_id: number | null) => {
    if (!event_id) return '-'
    return events.find(e => e.id === event_id)?.name ?? '-'
  }

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Teams</h1>
        <button className="btn btn-primary" onClick={openCreateModal}>
          <Plus size={18} /> Create Team
        </button>
      </div>

      <div className="table-container">
        <table className="table">
          <thead>
            <tr>
              <th>Team Name</th>
              <th>Category</th>
              <th>Score</th>
              <th>Event</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={5} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>Loading...</td></tr>
            ) : teams.map((team) => (
              <tr key={team.id} className={team.category === 'red' ? 'table-row-red' : team.category === 'blue' ? 'table-row-blue' : ''}>
                <td style={{ fontWeight: 500 }}>{team.name}</td>
                <td>{getCategoryBadge(team.category)}</td>
                <td>{team.score && team.score > 0 ? getScoreBadge(team.score) : '-'}</td>
                <td>{getEventName(team.event_id)}</td>
                <td>
                  <div className="action-buttons">
                    <button className="btn btn-ghost btn-sm" onClick={() => openEditModal(team)}>
                      <Edit size={14} />
                    </button>
                    <button className="btn btn-ghost btn-sm" style={{ color: 'var(--error)' }} onClick={() => handleDelete(team.id)}>
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
        title={editingTeam ? 'Edit Team' : 'Create Team'}
        footer={
          <>
            <button className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>Cancel</button>
            <button className="btn btn-primary" onClick={handleSubmit}>
              {editingTeam ? 'Save Changes' : 'Create Team'}
            </button>
          </>
        }
      >
        <div className="form-group">
          <label className="form-label">Team Name</label>
          <input type="text" className="form-input" value={formData.name}
            onChange={(e) => setFormData({ ...formData, name: e.target.value })} placeholder="Enter team name" />
        </div>
        <div className="form-group">
          <label className="form-label">Category</label>
          <select className="form-select w-full" value={formData.category}
            onChange={(e) => setFormData({ ...formData, category: e.target.value as any })}>
            <option value="general">General</option>
            <option value="red">Red Team</option>
            <option value="blue">Blue Team</option>
          </select>
        </div>
        <div className="form-group">
          <label className="form-label">Event</label>
          <select className="form-select w-full" value={formData.event_id}
            onChange={(e) => setFormData({ ...formData, event_id: e.target.value })}>
            <option value="">Select event</option>
            {events.map((event) => (
              <option key={event.id} value={event.id}>{event.name}</option>
            ))}
          </select>
        </div>
      </Modal>
    </div>
  )
}
