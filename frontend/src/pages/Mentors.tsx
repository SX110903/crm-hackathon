import { useState } from 'react'
import { Plus, Edit, Trash2 } from 'lucide-react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Modal } from '@/components/ui/Modal'
import { mentorsApi } from '@/lib/api'
import type { Mentor } from '@/lib/types'

export function Mentors() {
  const queryClient = useQueryClient()
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingMentor, setEditingMentor] = useState<Mentor | null>(null)

  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    company: '',
    specialization: '',
  })

  const { data: mentors = [], isLoading } = useQuery<Mentor[]>({
    queryKey: ['mentors'],
    queryFn: () => mentorsApi.getAll().then(r => r.data.data),
  })

  const createMutation = useMutation({
    mutationFn: (data: unknown) => mentorsApi.create(data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['mentors'] }),
  })

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: unknown }) => mentorsApi.update(id, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['mentors'] }),
  })

  const deleteMutation = useMutation({
    mutationFn: (id: number) => mentorsApi.delete(id),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['mentors'] }),
  })

  const openCreateModal = () => {
    setEditingMentor(null)
    setFormData({ first_name: '', last_name: '', email: '', company: '', specialization: '' })
    setIsModalOpen(true)
  }

  const openEditModal = (mentor: Mentor) => {
    setEditingMentor(mentor)
    setFormData({
      first_name: mentor.first_name,
      last_name: mentor.last_name,
      email: mentor.email,
      company: mentor.company ?? '',
      specialization: mentor.specialization ?? '',
    })
    setIsModalOpen(true)
  }

  const handleSubmit = () => {
    if (editingMentor) {
      updateMutation.mutate({ id: editingMentor.id, data: formData })
    } else {
      createMutation.mutate(formData)
    }
    setIsModalOpen(false)
  }

  const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this mentor?')) {
      deleteMutation.mutate(id)
    }
  }

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Mentors</h1>
        <button className="btn btn-primary" onClick={openCreateModal}>
          <Plus size={18} /> Add Mentor
        </button>
      </div>

      {isLoading ? (
        <div style={{ color: 'var(--text-muted)' }}>Loading...</div>
      ) : (
        <div className="cards-grid">
          {mentors.map((mentor) => (
            <div key={mentor.id} className="card">
              <div className="card-body">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '16px' }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                    <div style={{ width: '48px', height: '48px', borderRadius: '50%', backgroundColor: 'var(--success-light)', color: 'var(--success)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 700, fontSize: '18px' }}>
                      {mentor.first_name.charAt(0)}
                    </div>
                    <div>
                      <div style={{ fontWeight: 600 }}>{mentor.first_name} {mentor.last_name}</div>
                      <div style={{ fontSize: '12px', color: 'var(--text-muted)' }}>{mentor.email}</div>
                    </div>
                  </div>
                  <div className="action-buttons">
                    <button className="btn btn-ghost btn-sm" onClick={() => openEditModal(mentor)}><Edit size={14} /></button>
                    <button className="btn btn-ghost btn-sm" style={{ color: 'var(--error)' }} onClick={() => handleDelete(mentor.id)}><Trash2 size={14} /></button>
                  </div>
                </div>
                {mentor.company && (
                  <div style={{ fontSize: '14px', color: 'var(--text-secondary)', marginBottom: '8px' }}>
                    <strong>Company:</strong> {mentor.company}
                  </div>
                )}
                {mentor.specialization && (
                  <div style={{ fontSize: '14px', color: 'var(--text-secondary)', marginBottom: '8px' }}>
                    <strong>Specialization:</strong> {mentor.specialization}
                  </div>
                )}
                {mentor.available_slots !== null && (
                  <div style={{ fontSize: '14px', color: 'var(--text-muted)' }}>
                    Available slots: {mentor.available_slots}
                  </div>
                )}
              </div>
            </div>
          ))}
        </div>
      )}

      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title={editingMentor ? 'Edit Mentor' : 'Add Mentor'}
        footer={
          <>
            <button className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>Cancel</button>
            <button className="btn btn-primary" onClick={handleSubmit}>
              {editingMentor ? 'Save Changes' : 'Add Mentor'}
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
          <label className="form-label">Company</label>
          <input type="text" className="form-input" value={formData.company}
            onChange={(e) => setFormData({ ...formData, company: e.target.value })} placeholder="Company name" />
        </div>
        <div className="form-group">
          <label className="form-label">Specialization</label>
          <input type="text" className="form-input" value={formData.specialization}
            onChange={(e) => setFormData({ ...formData, specialization: e.target.value })} placeholder="e.g. Full Stack Development" />
        </div>
      </Modal>
    </div>
  )
}
