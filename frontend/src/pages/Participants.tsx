import { useState } from 'react'
import { Plus, Edit, Trash2, Search } from 'lucide-react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Modal } from '@/components/ui/Modal'
import { participantsApi } from '@/lib/api'
import type { Participant } from '@/lib/types'

export function Participants() {
  const queryClient = useQueryClient()
  const [searchTerm, setSearchTerm] = useState('')
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingParticipant, setEditingParticipant] = useState<Participant | null>(null)

  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    university: '',
    major: '',
  })

  const { data: participants = [], isLoading } = useQuery<Participant[]>({
    queryKey: ['participants'],
    queryFn: () => participantsApi.getAll().then(r => r.data.data),
  })

  const createMutation = useMutation({
    mutationFn: (data: unknown) => participantsApi.create(data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['participants'] }),
  })

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: unknown }) => participantsApi.update(id, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['participants'] }),
  })

  const deleteMutation = useMutation({
    mutationFn: (id: number) => participantsApi.delete(id),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['participants'] }),
  })

  const filtered = participants.filter((p) => {
    const fullName = `${p.first_name} ${p.last_name}`.toLowerCase()
    return fullName.includes(searchTerm.toLowerCase()) || p.email.toLowerCase().includes(searchTerm.toLowerCase())
  })

  const openCreateModal = () => {
    setEditingParticipant(null)
    setFormData({ first_name: '', last_name: '', email: '', phone: '', university: '', major: '' })
    setIsModalOpen(true)
  }

  const openEditModal = (p: Participant) => {
    setEditingParticipant(p)
    setFormData({
      first_name: p.first_name,
      last_name: p.last_name,
      email: p.email,
      phone: p.phone ?? '',
      university: p.university ?? '',
      major: p.major ?? '',
    })
    setIsModalOpen(true)
  }

  const handleSubmit = () => {
    if (editingParticipant) {
      updateMutation.mutate({ id: editingParticipant.id, data: formData })
    } else {
      createMutation.mutate(formData)
    }
    setIsModalOpen(false)
  }

  const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this participant?')) {
      deleteMutation.mutate(id)
    }
  }

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Participants</h1>
        <button className="btn btn-primary" onClick={openCreateModal}>
          <Plus size={18} /> Add Participant
        </button>
      </div>

      <div className="search-bar">
        <div className="search-input-wrapper">
          <Search size={18} />
          <input
            type="text"
            className="form-input"
            style={{ paddingLeft: '40px' }}
            placeholder="Search participants..."
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
        </div>
      </div>

      <div className="table-container">
        <table className="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>University</th>
              <th>Major</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={5} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>Loading...</td></tr>
            ) : filtered.map((p) => (
              <tr key={p.id}>
                <td style={{ fontWeight: 500 }}>{p.first_name} {p.last_name}</td>
                <td>{p.email}</td>
                <td>{p.university ?? '-'}</td>
                <td>{p.major ?? '-'}</td>
                <td>
                  <div className="action-buttons">
                    <button className="btn btn-ghost btn-sm" onClick={() => openEditModal(p)}>
                      <Edit size={14} />
                    </button>
                    <button className="btn btn-ghost btn-sm" style={{ color: 'var(--error)' }} onClick={() => handleDelete(p.id)}>
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
        title={editingParticipant ? 'Edit Participant' : 'Add Participant'}
        footer={
          <>
            <button className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>Cancel</button>
            <button className="btn btn-primary" onClick={handleSubmit}>
              {editingParticipant ? 'Save Changes' : 'Add Participant'}
            </button>
          </>
        }
      >
        <div className="form-group">
          <label className="form-label">First Name</label>
          <input type="text" className="form-input" value={formData.first_name}
            onChange={(e) => setFormData({ ...formData, first_name: e.target.value })} placeholder="First name" />
        </div>
        <div className="form-group">
          <label className="form-label">Last Name</label>
          <input type="text" className="form-input" value={formData.last_name}
            onChange={(e) => setFormData({ ...formData, last_name: e.target.value })} placeholder="Last name" />
        </div>
        <div className="form-group">
          <label className="form-label">Email</label>
          <input type="email" className="form-input" value={formData.email}
            onChange={(e) => setFormData({ ...formData, email: e.target.value })} placeholder="Email address" />
        </div>
        <div className="form-group">
          <label className="form-label">Phone</label>
          <input type="text" className="form-input" value={formData.phone}
            onChange={(e) => setFormData({ ...formData, phone: e.target.value })} placeholder="Phone number" />
        </div>
        <div className="form-group">
          <label className="form-label">University</label>
          <input type="text" className="form-input" value={formData.university}
            onChange={(e) => setFormData({ ...formData, university: e.target.value })} placeholder="University name" />
        </div>
        <div className="form-group">
          <label className="form-label">Major</label>
          <input type="text" className="form-input" value={formData.major}
            onChange={(e) => setFormData({ ...formData, major: e.target.value })} placeholder="Field of study" />
        </div>
      </Modal>
    </div>
  )
}
