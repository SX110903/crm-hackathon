import { useState } from 'react'
import { Plus, Trophy, Link as LinkIcon } from 'lucide-react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Modal } from '@/components/ui/Modal'
import { Badge } from '@/components/ui/Badge'
import { awardsApi, projectsApi } from '@/lib/api'
import type { Award, Project } from '@/lib/types'

export function Awards() {
  const queryClient = useQueryClient()
  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false)
  const [isAssignModalOpen, setIsAssignModalOpen] = useState(false)
  const [selectedAward, setSelectedAward] = useState<Award | null>(null)

  const [createFormData, setCreateFormData] = useState({ name: '', category: '', prize: '' })
  const [assignProjectId, setAssignProjectId] = useState('')

  const { data: awards = [], isLoading } = useQuery<Award[]>({
    queryKey: ['awards'],
    queryFn: () => awardsApi.getAll().then(r => r.data.data),
  })

  const { data: projects = [] } = useQuery<Project[]>({
    queryKey: ['projects'],
    queryFn: () => projectsApi.getAll().then(r => r.data.data),
  })

  const createMutation = useMutation({
    mutationFn: (data: unknown) => awardsApi.create(data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['awards'] }),
  })

  const assignMutation = useMutation({
    mutationFn: ({ id, project_id }: { id: number; project_id: number }) => awardsApi.assign(id, project_id),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['awards'] }),
  })

  const handleCreate = () => {
    createMutation.mutate(createFormData)
    setIsCreateModalOpen(false)
    setCreateFormData({ name: '', category: '', prize: '' })
  }

  const openAssignModal = (award: Award) => {
    setSelectedAward(award)
    setAssignProjectId('')
    setIsAssignModalOpen(true)
  }

  const handleAssign = () => {
    if (selectedAward && assignProjectId) {
      assignMutation.mutate({ id: selectedAward.id, project_id: Number(assignProjectId) })
      setIsAssignModalOpen(false)
    }
  }

  const getProjectName = (project_id: number | null) => {
    if (!project_id) return null
    return projects.find(p => p.id === project_id)?.name ?? null
  }

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Awards</h1>
        <button className="btn btn-primary" onClick={() => setIsCreateModalOpen(true)}>
          <Plus size={18} /> Create Award
        </button>
      </div>

      {isLoading ? (
        <div style={{ color: 'var(--text-muted)' }}>Loading...</div>
      ) : (
        <div className="cards-grid">
          {awards.map((award) => (
            <div key={award.id} className="award-card">
              <div className="award-icon"><Trophy size={24} /></div>
              <h3 className="award-name">{award.name}</h3>
              <p className="award-category">{award.category}</p>
              <p className="award-prize">{award.prize}</p>
              {award.project_id ? (
                <div>
                  <Badge variant="gold">Awarded</Badge>
                  <p className="award-project" style={{ marginTop: '8px' }}>
                    Awarded to: <strong>{getProjectName(award.project_id)}</strong>
                  </p>
                  {award.awarded_date && (
                    <p style={{ fontSize: '12px', color: 'var(--text-muted)' }}>
                      {new Date(award.awarded_date).toLocaleDateString()}
                    </p>
                  )}
                </div>
              ) : (
                <div>
                  <Badge variant="gray">Unassigned</Badge>
                  <button className="btn btn-secondary btn-sm w-full" style={{ marginTop: '12px' }} onClick={() => openAssignModal(award)}>
                    <LinkIcon size={14} /> Assign to Project
                  </button>
                </div>
              )}
            </div>
          ))}
        </div>
      )}

      <Modal
        isOpen={isCreateModalOpen}
        onClose={() => setIsCreateModalOpen(false)}
        title="Create Award"
        footer={
          <>
            <button className="btn btn-secondary" onClick={() => setIsCreateModalOpen(false)}>Cancel</button>
            <button className="btn btn-primary" onClick={handleCreate}>Create Award</button>
          </>
        }
      >
        <div className="form-group">
          <label className="form-label">Award Name</label>
          <input type="text" className="form-input" value={createFormData.name}
            onChange={(e) => setCreateFormData({ ...createFormData, name: e.target.value })} placeholder="e.g. Best Overall Project" />
        </div>
        <div className="form-group">
          <label className="form-label">Category</label>
          <input type="text" className="form-input" value={createFormData.category}
            onChange={(e) => setCreateFormData({ ...createFormData, category: e.target.value })} placeholder="e.g. Grand Prize, Innovation" />
        </div>
        <div className="form-group">
          <label className="form-label">Prize</label>
          <input type="text" className="form-input" value={createFormData.prize}
            onChange={(e) => setCreateFormData({ ...createFormData, prize: e.target.value })} placeholder="e.g. $5,000" />
        </div>
      </Modal>

      <Modal
        isOpen={isAssignModalOpen}
        onClose={() => setIsAssignModalOpen(false)}
        title={`Assign "${selectedAward?.name}"`}
        footer={
          <>
            <button className="btn btn-secondary" onClick={() => setIsAssignModalOpen(false)}>Cancel</button>
            <button className="btn btn-primary" onClick={handleAssign} disabled={!assignProjectId}>Assign Award</button>
          </>
        }
      >
        <div className="form-group">
          <label className="form-label">Select Project</label>
          <select className="form-select w-full" value={assignProjectId}
            onChange={(e) => setAssignProjectId(e.target.value)}>
            <option value="">Select a project</option>
            {projects.map((p) => (
              <option key={p.id} value={p.id}>{p.name}</option>
            ))}
          </select>
        </div>
        {assignProjectId && (
          <div style={{ padding: '12px', backgroundColor: 'var(--body-bg)', borderRadius: 'var(--radius-md)' }}>
            <p style={{ fontSize: '14px', color: 'var(--text-secondary)' }}>
              This will award <strong>{selectedAward?.name}</strong> ({selectedAward?.prize}) to{' '}
              <strong>{projects.find(p => p.id === Number(assignProjectId))?.name}</strong>
            </p>
          </div>
        )}
      </Modal>
    </div>
  )
}
