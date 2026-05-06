import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { createParticipant, updateParticipant, getParticipant, ParticipantPayload } from '../../api/participants';

const initialForm: ParticipantPayload = {
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  university: '',
  major: '',
  year_of_study: undefined,
};

const ParticipantForm: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const isEdit = !!id;

  const [form, setForm] = useState<ParticipantPayload>(initialForm);
  const [loading, setLoading] = useState(false);
  const [fetching, setFetching] = useState(isEdit);
  const [error, setError] = useState('');
  const [errors, setErrors] = useState<Record<string, string>>({});

  useEffect(() => {
    if (isEdit && id) {
      setFetching(true);
      getParticipant(Number(id))
        .then(r => {
          const p = r.data.data ?? r.data;
          setForm({
            first_name: p.first_name ?? '',
            last_name: p.last_name ?? '',
            email: p.email ?? '',
            phone: p.phone ?? '',
            university: p.university ?? '',
            major: p.major ?? '',
            year_of_study: p.year_of_study ?? undefined,
          });
        })
        .catch(() => setError('Failed to load participant data.'))
        .finally(() => setFetching(false));
    }
  }, [id, isEdit]);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setForm(prev => ({
      ...prev,
      [name]: name === 'year_of_study' ? (value ? Number(value) : undefined) : value,
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
        await updateParticipant(Number(id), form);
      } else {
        await createParticipant(form);
      }
      navigate('/participants');
    } catch (err: any) {
      if (err.response?.data?.errors) {
        const fieldErrors: Record<string, string> = {};
        Object.entries(err.response.data.errors).forEach(([key, val]) => {
          fieldErrors[key] = Array.isArray(val) ? (val as string[])[0] : String(val);
        });
        setErrors(fieldErrors);
      } else {
        setError(err.response?.data?.message || 'An error occurred. Please try again.');
      }
    } finally {
      setLoading(false);
    }
  };

  if (fetching) {
    return (
      <div className="loading-container">
        <div className="spinner"></div>
      </div>
    );
  }

  return (
    <div className="form-page">
      <div className="page-header">
        <h1 className="page-heading">{isEdit ? 'Edit Participant' : 'Add Participant'}</h1>
      </div>

      <div className="form-card">
        {error && <div className="alert alert-error">{error}</div>}

        <form onSubmit={handleSubmit}>
          <div className="form-grid form-grid-2">
            <div className="form-group">
              <label className="form-label">First Name *</label>
              <input
                name="first_name"
                type="text"
                className={`form-input ${errors.first_name ? 'form-input-error' : ''}`}
                value={form.first_name}
                onChange={handleChange}
                required
              />
              {errors.first_name && <span className="form-error">{errors.first_name}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Last Name *</label>
              <input
                name="last_name"
                type="text"
                className={`form-input ${errors.last_name ? 'form-input-error' : ''}`}
                value={form.last_name}
                onChange={handleChange}
                required
              />
              {errors.last_name && <span className="form-error">{errors.last_name}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Email *</label>
              <input
                name="email"
                type="email"
                className={`form-input ${errors.email ? 'form-input-error' : ''}`}
                value={form.email}
                onChange={handleChange}
                required
              />
              {errors.email && <span className="form-error">{errors.email}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Phone</label>
              <input
                name="phone"
                type="tel"
                className={`form-input ${errors.phone ? 'form-input-error' : ''}`}
                value={form.phone ?? ''}
                onChange={handleChange}
              />
              {errors.phone && <span className="form-error">{errors.phone}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">University</label>
              <input
                name="university"
                type="text"
                className={`form-input ${errors.university ? 'form-input-error' : ''}`}
                value={form.university ?? ''}
                onChange={handleChange}
              />
              {errors.university && <span className="form-error">{errors.university}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Major</label>
              <input
                name="major"
                type="text"
                className={`form-input ${errors.major ? 'form-input-error' : ''}`}
                value={form.major ?? ''}
                onChange={handleChange}
              />
              {errors.major && <span className="form-error">{errors.major}</span>}
            </div>

            <div className="form-group">
              <label className="form-label">Year of Study</label>
              <select
                name="year_of_study"
                className={`form-select ${errors.year_of_study ? 'form-input-error' : ''}`}
                value={form.year_of_study ?? ''}
                onChange={handleChange}
              >
                <option value="">Select year</option>
                {[1, 2, 3, 4, 5, 6].map(y => (
                  <option key={y} value={y}>Year {y}</option>
                ))}
              </select>
              {errors.year_of_study && <span className="form-error">{errors.year_of_study}</span>}
            </div>
          </div>

          <div className="form-actions">
            <button
              type="button"
              className="btn btn-secondary"
              onClick={() => navigate('/participants')}
            >
              Cancel
            </button>
            <button type="submit" className="btn btn-primary" disabled={loading}>
              {loading ? 'Saving...' : isEdit ? 'Update Participant' : 'Create Participant'}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default ParticipantForm;
