import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { createTeam, updateTeam, getTeam, TeamPayload } from '../../api/teams';
import { getParticipants, Participant } from '../../api/participants';

const initialForm: TeamPayload = {
  name: '',
  max_members: 4,
  leader_id: undefined,
};

const TeamForm: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const isEdit = !!id;

  const [form, setForm] = useState<TeamPayload>(initialForm);
  const [participants, setParticipants] = useState<Participant[]>([]);
  const [loading, setLoading] = useState(false);
  const [fetching, setFetching] = useState(isEdit);
  const [error, setError] = useState('');
  const [errors, setErrors] = useState<Record<string, string>>({});

  useEffect(() => {
    getParticipants(1, '').then(r => {
      setParticipants(r.data?.data ?? r.data ?? []);
    }).catch(() => {});
  }, []);

  useEffect(() => {
    if (isEdit && id) {
      setFetching(true);
      getTeam(Number(id))
        .then(r => {
          const t = r.data.data ?? r.data;
          setForm({
            name: t.name ?? '',
            max_members: t.max_members ?? 4,
            leader_id: t.leader_id ?? undefined,
          });
        })
        .catch(() => setError('Failed to load team data.'))
        .finally(() => setFetching(false));
    }
  }, [id, isEdit]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setForm(prev => ({
      ...prev,
      [name]: name === 'max_members' || name === 'leader_id'
        ? (value ? Number(value) : undefined)
        : value,
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
        await updateTeam(Number(id), form);
      } else {
        await createTeam(form);
      }
      navigate('/teams');
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
        <h1 className="page-heading">{isEdit ? 'Edit Team' : 'Add Team'}</h1>
      </div>
      <div className="form-card">
        {error && <div className="alert alert-error">{error}</div>}
        <form onSubmit={handleSubmit}>
          <div className="form-grid form-grid-2">
            <div className="form-group">
              <label className="form-label">Team Name *</label>
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

            <div className="form-group">
              <label className="form-label">Max Members *</label>
              <input
                name="max_members"
                type="number"
                min={1}
                max={20}
                className={`form-input ${errors.max_members ? 'form-input-error' : ''}`}
                value={form.max_members}
                onChange={handleChange}
                required
              />
              {errors.max_members && <span className="form-error">{errors.max_members}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Team Leader</label>
              <select
                name="leader_id"
                className={`form-select ${errors.leader_id ? 'form-input-error' : ''}`}
                value={form.leader_id ?? ''}
                onChange={handleChange}
              >
                <option value="">Select leader (optional)</option>
                {participants.map(p => (
                  <option key={p.id} value={p.id}>
                    {p.first_name} {p.last_name} ({p.email})
                  </option>
                ))}
              </select>
              {errors.leader_id && <span className="form-error">{errors.leader_id}</span>}
            </div>
          </div>

          <div className="form-actions">
            <button type="button" className="btn btn-secondary" onClick={() => navigate('/teams')}>Cancel</button>
            <button type="submit" className="btn btn-primary" disabled={loading}>
              {loading ? 'Saving...' : isEdit ? 'Update Team' : 'Create Team'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default TeamForm;
