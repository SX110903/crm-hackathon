import { useState } from 'react'
import { Plus } from 'lucide-react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Modal } from '@/components/ui/Modal'
import { ScoreSlider } from '@/components/ui/ScoreSlider'
import { getScoreBadge } from '@/components/ui/Badge'
import { evaluationsApi, projectsApi, judgesApi } from '@/lib/api'
import type { Evaluation, Project, Judge } from '@/lib/types'

export function Evaluations() {
  const queryClient = useQueryClient()
  const [isModalOpen, setIsModalOpen] = useState(false)

  const [formData, setFormData] = useState({
    project_id: '' as string | number,
    judge_id: '' as string | number,
    innovation_score: 7,
    technical_score: 7,
    presentation_score: 7,
    usability_score: 7,
    comments: '',
  })

  const totalScore = (formData.innovation_score + formData.technical_score + formData.presentation_score + formData.usability_score) / 4

  const { data: evaluations = [], isLoading } = useQuery<Evaluation[]>({
    queryKey: ['evaluations'],
    queryFn: () => evaluationsApi.getAll().then(r => r.data.data),
  })

  const { data: projects = [] } = useQuery<Project[]>({
    queryKey: ['projects'],
    queryFn: () => projectsApi.getAll().then(r => r.data.data),
  })

  const { data: judges = [] } = useQuery<Judge[]>({
    queryKey: ['judges'],
    queryFn: () => judgesApi.getAll().then(r => r.data.data),
  })

  const createMutation = useMutation({
    mutationFn: (data: unknown) => evaluationsApi.create(data),
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['evaluations'] }),
  })

  const openCreateModal = () => {
    setFormData({ project_id: '', judge_id: '', innovation_score: 7, technical_score: 7, presentation_score: 7, usability_score: 7, comments: '' })
    setIsModalOpen(true)
  }

  const handleSubmit = () => {
    createMutation.mutate({
      project_id: formData.project_id,
      judge_id: formData.judge_id,
      innovation_score: formData.innovation_score,
      technical_score: formData.technical_score,
      presentation_score: formData.presentation_score,
      usability_score: formData.usability_score,
      comments: formData.comments,
    })
    setIsModalOpen(false)
  }

  const getProjectName = (id: number) => projects.find(p => p.id === id)?.name ?? String(id)
  const getJudgeName = (id: number) => {
    const j = judges.find(j => j.id === id)
    return j ? `${j.first_name} ${j.last_name}` : String(id)
  }

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Evaluations</h1>
        <button className="btn btn-primary" onClick={openCreateModal}>
          <Plus size={18} /> New Evaluation
        </button>
      </div>

      <div className="table-container">
        <table className="table">
          <thead>
            <tr>
              <th>Project</th>
              <th>Judge</th>
              <th>Innovation</th>
              <th>Technical</th>
              <th>Presentation</th>
              <th>Usability</th>
              <th>Total Score</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            {isLoading ? (
              <tr><td colSpan={8} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>Loading...</td></tr>
            ) : evaluations.map((ev) => (
              <tr key={ev.id}>
                <td style={{ fontWeight: 500 }}>{getProjectName(ev.project_id)}</td>
                <td>{getJudgeName(ev.judge_id)}</td>
                <td>{ev.innovation_score.toFixed(1)}</td>
                <td>{ev.technical_score.toFixed(1)}</td>
                <td>{ev.presentation_score.toFixed(1)}</td>
                <td>{ev.usability_score.toFixed(1)}</td>
                <td>{getScoreBadge(ev.total_score)}</td>
                <td style={{ fontSize: '12px', color: 'var(--text-muted)' }}>
                  {new Date(ev.created_at).toLocaleDateString()}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      <Modal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        title="New Evaluation"
        footer={
          <>
            <button className="btn btn-secondary" onClick={() => setIsModalOpen(false)}>Cancel</button>
            <button className="btn btn-primary" onClick={handleSubmit} disabled={!formData.project_id || !formData.judge_id}>
              Submit Evaluation
            </button>
          </>
        }
      >
        <div className="form-group">
          <label className="form-label">Project</label>
          <select className="form-select w-full" value={formData.project_id}
            onChange={(e) => setFormData({ ...formData, project_id: e.target.value })}>
            <option value="">Select project</option>
            {projects.map((p) => (
              <option key={p.id} value={p.id}>{p.name}</option>
            ))}
          </select>
        </div>

        <div className="form-group">
          <label className="form-label">Judge</label>
          <select className="form-select w-full" value={formData.judge_id}
            onChange={(e) => setFormData({ ...formData, judge_id: e.target.value })}>
            <option value="">Select judge</option>
            {judges.map((j) => (
              <option key={j.id} value={j.id}>{j.first_name} {j.last_name}{j.expertise ? ` - ${j.expertise}` : ''}</option>
            ))}
          </select>
        </div>

        <div style={{ marginTop: '24px', marginBottom: '16px' }}>
          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '16px' }}>
            <span style={{ fontWeight: 600 }}>Scoring</span>
            <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
              <span style={{ fontSize: '14px', color: 'var(--text-muted)' }}>Total:</span>
              <span style={{ fontSize: '24px', fontWeight: 700, color: totalScore >= 8 ? 'var(--success)' : totalScore >= 6 ? 'var(--warning)' : 'var(--error)' }}>
                {totalScore.toFixed(1)}
              </span>
            </div>
          </div>
          <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
            <ScoreSlider label="Innovation" value={formData.innovation_score} onChange={(v) => setFormData({ ...formData, innovation_score: v })} />
            <ScoreSlider label="Technical" value={formData.technical_score} onChange={(v) => setFormData({ ...formData, technical_score: v })} />
            <ScoreSlider label="Presentation" value={formData.presentation_score} onChange={(v) => setFormData({ ...formData, presentation_score: v })} />
            <ScoreSlider label="Usability" value={formData.usability_score} onChange={(v) => setFormData({ ...formData, usability_score: v })} />
          </div>
        </div>

        <div className="form-group">
          <label className="form-label">Comments</label>
          <textarea className="form-textarea" value={formData.comments}
            onChange={(e) => setFormData({ ...formData, comments: e.target.value })} placeholder="Add comments..." />
        </div>
      </Modal>
    </div>
  )
}
