import { useState } from 'react'
import { Plus, Edit, Trash2, MapPin, Calendar } from 'lucide-react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Modal } from '@/components/ui/Modal'
import { getStatusBadge } from '@/components/ui/Badge'
import { eventsApi } from '@/lib/api'
import type { Event } from '@/lib/types'

export function Events() {
  const queryClient = useQueryClient()
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingEvent, setEditingEvent] = useState<Event | null>(null)

  const [formData, setFormData] = useState({
    name: '',
    description: '',
    start_date: '',
    end_date: '',
    location: '',
    max_teams: 50,
    category_type: 'general' as 'general' | 'red_vs_blue' | 'mixed',
  })

  const { data: events = [], isLoading } = useQuery<Event[]>({
    queryKey: ['events'],
    queryFn: () => eventsApi.getAll().then(r => r.data.data),
  })

  const createMutation = useMutation({
    mutationFn: (data: unknown) => eventsApi.create(data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['events'] }),
  })

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: unknown }) => eventsApi.update(id, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['events'] }),
  })

  const deleteMutation = useMutation({
    mutationFn: (id: number) => eventsApi.delete(id),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['events'] }),
  })

  const openCreateModal = () => {
    setEditingEvent(null)
    setFormData({ name: '', description: '', start_date: '', end_date: '', location: '', max_teams: 50, category_type: 'general' })
    setIsModalOpen(true)
  }

  const openEditModal = (event: Event) => {
    setEditingEvent(event)
    setFormData({
      name: event.name,
      description: event.description ?? '',
      start_date: event.start_date,
      end_date: event.end_date,
      location: event.location ?? '',
      max_teams: event.max_teams,
      category_type: event.category_type,
    })
    setIsModalOpen(true)
  }

  const handleSubmit = () => {
    if (editingEvent) {
      updateMutation.mutate({ id: editingEvent.id, data: formData })
    } else {
      createMutation.mutate(formData)
    }
    setIsModalOpen(false)
  }

  const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this event?')) {
      deleteMutation.mutate(id)
    }
  }

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Events</h1>
        <button className="btn btn-primary" onClick={openCreateModal}>
          <Plus size={18} /> Create Event
        </button>
      </div>

      <div className="table-container">
        <table className="table">
          <thead>
            <tr>
              <th>Event Name</th>
              <th>Date</th>
              <th>Location</th>
              <th>Status</th>
              <th>Teams</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={6} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>Loading...</td></tr>
            ) : events.map((event) => (
              <tr key={event.id}>
                <td>
                  <div style={{ fontWeight: 500 }}>{event.name}</div>
                  <div style={{ fontSize: '12px', color: 'var(--text-muted)' }}>
                    {event.category_type === 'red_vs_blue' ? 'Red vs Blue' : event.category_type === 'mixed' ? 'Mixed' : 'General'}
                  </div>
                </td>
                <td>
                  <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                    <Calendar size={14} style={{ color: 'var(--text-muted)' }} />
                    {event.start_date} - {event.end_date}
                  </div>
                </td>
                <td>
                  <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                    <MapPin size={14} style={{ color: 'var(--text-muted)' }} />
                    {event.location ?? '-'}
                  </div>
                </td>
                <td>{getStatusBadge(event.status)}</td>
                <td>{event.teams_count ?? 0} / {event.max_teams}</td>
                <td>
                  <div className="action-buttons">
                    <button className="btn btn-ghost btn-sm" onClick={() => openEditModal(event)}><Edit size={14} /></button>
                    <button className="btn btn-ghost btn-sm" style={{ color: 'var(--error)' }} onClick={() => handleDelete(event.id)}><Trash2 size={14} /></button>
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
        title={editingEvent ? 'Edit Event' : 'Create Event'}
        footer={
          <>
            <button className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>Cancel</button>
            <button className="btn btn-primary" onClick={handleSubmit}>
              {editingEvent ? 'Save Changes' : 'Create Event'}
            </button>
          </>
        }
      >
        <div className="form-group">
          <label className="form-label">Event Name</label>
          <input type="text" className="form-input" value={formData.name}
            onChange={(e) => setFormData({ ...formData, name: e.target.value })} placeholder="Enter event name" />
        </div>
        <div className="form-group">
          <label className="form-label">Description</label>
          <textarea className="form-textarea" value={formData.description}
            onChange={(e) => setFormData({ ...formData, description: e.target.value })} placeholder="Event description" />
        </div>
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
          <div className="form-group">
            <label className="form-label">Start Date</label>
            <input type="date" className="form-input" value={formData.start_date}
              onChange={(e) => setFormData({ ...formData, start_date: e.target.value })} />
          </div>
          <div className="form-group">
            <label className="form-label">End Date</label>
            <input type="date" className="form-input" value={formData.end_date}
              onChange={(e) => setFormData({ ...formData, end_date: e.target.value })} />
          </div>
        </div>
        <div className="form-group">
          <label className="form-label">Location</label>
          <input type="text" className="form-input" value={formData.location}
            onChange={(e) => setFormData({ ...formData, location: e.target.value })} placeholder="Event location" />
        </div>
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '16px' }}>
          <div className="form-group">
            <label className="form-label">Max Teams</label>
            <input type="number" className="form-input" value={formData.max_teams}
              onChange={(e) => setFormData({ ...formData, max_teams: parseInt(e.target.value) })} />
          </div>
          <div className="form-group">
            <label className="form-label">Category Type</label>
            <select className="form-select w-full" value={formData.category_type}
              onChange={(e) => setFormData({ ...formData, category_type: e.target.value as any })}>
              <option value="general">General</option>
              <option value="red_vs_blue">Red Team vs Blue Team</option>
              <option value="mixed">Mixed</option>
            </select>
          </div>
        </div>
      </Modal>
    </div>
  )
}
