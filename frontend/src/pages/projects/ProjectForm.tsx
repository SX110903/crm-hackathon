import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { createProject, updateProject, getProject, ProjectPayload } from '../../api/projects';
import { getTeams, Team } from '../../api/teams';

const STATUS_OPTIONS = ['draft', 'submitted', 'approved', 'rejected'];
const CATEGORY_OPTIONS = ['Web', 'Mobile', 'AI/ML', 'IoT', 'Blockchain', 'Game', 'Other'];

const initialForm: ProjectPayload = {
  team_id: 0,
  name: '',
  description: '',
  category: '',
  technology_stack: '',
  github_url: '',
  demo_url: '',
  status: 'draft',
};

const ProjectForm: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const isEdit = !!id;

  const [form, setForm] = useState<ProjectPayload>(initialForm);
  const [teams, setTeams] = useState<Team[]>([]);
  const [loading, setLoading] = useState(false);
  const [fetching, setFetching] = useState(isEdit);
  const [error, setError] = useState('');
  const [errors, setErrors] = useState<Record<string, string>>({});

  useEffect(() => {
    getTeams(1, '').then(r => setTeams(r.data?.data ?? r.data ?? [])).catch(() => {});
  }, []);

  useEffect(() => {
    if (isEdit && id) {
      setFetching(true);
      getProject(Number(id))
        .then(r => {
          const p = r.data.data ?? r.data;
          setForm({
            team_id: p.team_id ?? 0,
            name: p.name ?? '',
            description: p.description ?? '',
            category: p.category ?? '',
            technology_stack: p.technology_stack ?? '',
            github_url: p.github_url ?? '',
            demo_url: p.demo_url ?? '',
            status: p.status ?? 'draft',
          });
        })
        .catch(() => setError('Failed to load project data.'))
        .finally(() => setFetching(false));
    }
  }, [id, isEdit]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setForm(prev => ({
      ...prev,
      [name]: name === 'team_id' ? Number(value) : value,
    }));
    if (errors[name]) setErrors(prev => ({ ...prev, [name]: '' }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setErrors({});
    setLoading(true);
    try {
      if (isEdit && id) {
        await updateProject(Number(id), form);
      } else {
        await createProject(form);
      }
      navigate('/projects');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        const fieldErrors: Record<string, string> = {};
        Object.entries(err.response.data.errors).forEach(([key, val]) => {
          fieldErrors[key] = Array.isArray(val) ? (val as string[])[0] : String(val);
        });
        setErrors(fieldErrors);
      } else {
        setError(err.response?.data?.message || 'An error occurred.');
      }
    } finally {
      setLoading(false);
    }
  };

  if (fetching) return <div className="loading-container"><div className="spinner"></div></div>;

  return (
    <div className="form-page">
      <div className="page-header">
        <h1 className="page-heading">{isEdit ? 'Edit Project' : 'Add Project'}</h1>
      </div>
      <div className="form-card">
        {error && <div className="alert alert-error">{error}</div>}
        <form onSubmit={handleSubmit}>
          <div className="form-grid form-grid-2">
            <div className="form-group">
              <label className="form-label">Team *</label>
              <select
                name="team_id"
                className={`form-select ${errors.team_id ? 'form-input-error' : ''}`}
                value={form.team_id || ''}
                onChange={handleChange}
                required
              >
                <option value="">Select a team</option>
                {teams.map(t => (
                  <option key={t.id} value={t.id}>{t.name}</option>
                ))}
              </select>
              {errors.team_id && <span className="form-error">{errors.team_id}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Project Name *</label>
              <input
                name="name"
                type="text"
                className={`form-input ${errors.name ? 'form-input-error' : ''}`}
                value={form.name}
                onChange={handleChange}
                required
              />
              {errors.name && <span className="form-error">{errors.name}</span>}
            </div>

            <div className="form-group form-group-full">
              <label className="form-label">Description</label>
              <textarea
                name="description"
                className={`form-input form-textarea ${errors.description ? 'form-input-error' : ''}`}
                value={form.description ?? ''}
                onChange={handleChange}
                rows={3}
              />
              {errors.description && <span className="form-error">{errors.description}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Category</label>
              <select
                name="category"
                className={`form-select ${errors.category ? 'form-input-error' : ''}`}
                value={form.category ?? ''}
                onChange={handleChange}
              >
                <option value="">Select category</option>
                {CATEGORY_OPTIONS.map(c => (
                  <option key={c} value={c}>{c}</option>
                ))}
              </select>
              {errors.category && <span className="form-error">{errors.category}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Technology Stack</label>
              <input
                name="technology_stack"
                type="text"
                className={`form-input ${errors.technology_stack ? 'form-input-error' : ''}`}
                value={form.technology_stack ?? ''}
                onChange={handleChange}
                placeholder="e.g. React, Node.js, PostgreSQL"
              />
              {errors.technology_stack && <span className="form-error">{errors.technology_stack}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">GitHub URL</label>
              <input
                name="github_url"
                type="url"
                className={`form-input ${errors.github_url ? 'form-input-error' : ''}`}
                value={form.github_url ?? ''}
                onChange={handleChange}
                placeholder="https://github.com/..."
              />
              {errors.github_url && <span className="form-error">{errors.github_url}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Demo URL</label>
              <input
                name="demo_url"
                type="url"
                className={`form-input ${errors.demo_url ? 'form-input-error' : ''}`}
                value={form.demo_url ?? ''}
                onChange={handleChange}
                placeholder="https://..."
              />
              {errors.demo_url && <span className="form-error">{errors.demo_url}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Status</label>
              <select
                name="status"
                className={`form-select ${errors.status ? 'form-input-error' : ''}`}
                value={form.status ?? 'draft'}
                onChange={handleChange}
              >
                {STATUS_OPTIONS.map(s => (
                  <option key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1)}</option>
                ))}
              </select>
              {errors.status && <span className="form-error">{errors.status}</span>}
            </div>
          </div>

          <div className="form-actions">
            <button type="button" className="btn btn-secondary" onClick={() => navigate('/projects')}>Cancel</button>
            <button type="submit" className="btn btn-primary" disabled={loading}>
              {loading ? 'Saving...' : isEdit ? 'Update Project' : 'Create Project'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default ProjectForm;
