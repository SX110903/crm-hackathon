import { useState } from 'react'
import { Plus, Edit, Trash2 } from 'lucide-react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Modal } from '@/components/ui/Modal'
import { judgesApi } from '@/lib/api'
import type { Judge } from '@/lib/types'

export function Judges() {
  const queryClient = useQueryClient()
  const [isModalOpen, setIsModalOpen] = useState(false)
  const [editingJudge, setEditingJudge] = useState<Judge | null>(null)

  const [formData, setFormData] = useState({
    first_name: '',
    last_name: '',
    email: '',
    company: '',
    expertise: '',
  })

  const { data: judges = [], isLoading } = useQuery<Judge[]>({
    queryKey: ['judges'],
    queryFn: () => judgesApi.getAll().then(r => r.data.data),
  })

  const createMutation = useMutation({
    mutationFn: (data: unknown) => judgesApi.create(data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['judges'] }),
  })

  const updateMutation = useMutation({
    mutationFn: ({ id, data }: { id: number; data: unknown }) => judgesApi.update(id, data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['judges'] }),
  })

  const deleteMutation = useMutation({
    mutationFn: (id: number) => judgesApi.delete(id),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['judges'] }),
  })

  const openCreateModal = () => {
    setEditingJudge(null)
    setFormData({ first_name: '', last_name: '', email: '', company: '', expertise: '' })
    setIsModalOpen(true)
  }

  const openEditModal = (judge: Judge) => {
    setEditingJudge(judge)
    setFormData({
      first_name: judge.first_name,
      last_name: judge.last_name,
      email: judge.email,
      company: judge.company ?? '',
      expertise: judge.expertise ?? '',
    })
    setIsModalOpen(true)
  }

  const handleSubmit = () => {
    if (editingJudge) {
      updateMutation.mutate({ id: editingJudge.id, data: formData })
    } else {
      createMutation.mutate(formData)
    }
    setIsModalOpen(false)
  }

  const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this judge?')) {
      deleteMutation.mutate(id)
    }
  }

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Judges</h1>
        <button className="btn btn-primary" onClick={openCreateModal}>
          <Plus size={18} /> Add Judge
        </button>
      </div>

      {isLoading ? (
        <div style={{ color: 'var(--text-muted)' }}>Loading...</div>
      ) : (
        <div className="cards-grid">
          {judges.map((judge) => (
            <div key={judge.id} className="card">
              <div className="card-body">
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '16px' }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                    <div style={{ width: '48px', height: '48px', borderRadius: '50%', backgroundColor: 'var(--primary-light)', color: 'var(--primary)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontWeight: 700, fontSize: '18px' }}>
                      {judge.first_name.charAt(0)}
                    </div>
                    <div>
                      <div style={{ fontWeight: 600 }}>{judge.first_name} {judge.last_name}</div>
                      <div style={{ fontSize: '12px', color: 'var(--text-muted)' }}>{judge.email}</div>
                    </div>
                  </div>
                  <div className="action-buttons">
                    <button className="btn btn-ghost btn-sm" onClick={() => openEditModal(judge)}><Edit size={14} /></button>
                    <button className="btn btn-ghost btn-sm" style={{ color: 'var(--error)' }} onClick={() => handleDelete(judge.id)}><Trash2 size={14} /></button>
                  </div>
                </div>
                {judge.company && (
                  <div style={{ fontSize: '14px', color: 'var(--text-secondary)', marginBottom: '8px' }}>
                    <strong>Company:</strong> {judge.company}
                  </div>
                )}
                {judge.expertise && (
                  <div style={{ fontSize: '14px', color: 'var(--text-secondary)' }}>
                    <strong>Expertise:</strong> {judge.expertise}
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
        title={editingJudge ? 'Edit Judge' : 'Add Judge'}
        footer={
          <>
            <button className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>Cancel</button>
            <button className="btn btn-primary" onClick={handleSubmit}>
              {editingJudge ? 'Save Changes' : 'Add Judge'}
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
          <label className="form-label">Expertise</label>
          <input type="text" className="form-input" value={formData.expertise}
            onChange={(e) => setFormData({ ...formData, expertise: e.target.value })} placeholder="e.g. AI/ML, Web Development" />
        </div>
      </Modal>
    </div>
  )
}
