import { useState } from 'react'
import { Link } from 'react-router-dom'
import { Zap, Check, Plus, Trash2, Users, Info, Calendar, Loader2 } from 'lucide-react'
import { useQuery } from '@tanstack/react-query'
import { Alert } from '@/components/ui/Alert'
import { eventsApi, registrationApi } from '@/lib/api'
import type { Event } from '@/lib/types'

interface TeamMember {
  name: string
  email: string
  role: string
}

export function Register() {
  const [step, setStep] = useState(1)
  const [loading, setLoading] = useState(false)
  const [success, setSuccess] = useState(false)
  const [error, setError] = useState('')

  const [selectedEvent, setSelectedEvent] = useState('')
  const [teamName, setTeamName] = useState('')
  const [category, setCategory] = useState<'red' | 'blue'>('red')
  const [members, setMembers] = useState<TeamMember[]>([{ name: '', email: '', role: 'Team Lead' }])

  const { data: activeEvents = [] } = useQuery<Event[]>({
    queryKey: ['events-active'],
    queryFn: () => eventsApi.getActive().then(r => r.data.data),
  })

  const addMember = () => {
    if (members.length < 5) setMembers([...members, { name: '', email: '', role: 'Developer' }])
  }

  const removeMember = (index: number) => {
    if (members.length > 1) setMembers(members.filter((_, i) => i !== index))
  }

  const updateMember = (index: number, field: keyof TeamMember, value: string) => {
    const updated = [...members]
    updated[index][field] = value
    setMembers(updated)
  }

  const handleSubmit = async () => {
    setLoading(true)
    setError('')
    try {
      await registrationApi.register({
        event_id: parseInt(selectedEvent),
        team_name: teamName,
        category,
        members,
      })
      setSuccess(true)
    } catch (err: unknown) {
      const msg =
        (err as { response?: { data?: { message?: string } } })?.response?.data?.message
        ?? 'Failed to register team. Please try again.'
      setError(msg)
    } finally {
      setLoading(false)
    }
  }

  const canProceed = () => {
    if (step === 1) return !!selectedEvent
    if (step === 2) return !!teamName && !!category
    if (step === 3) return members.every((m) => m.name && m.email && m.role)
    return false
  }

  if (success) {
    return (
      <div className="auth-page">
        <div className="auth-card" style={{ textAlign: 'center' }}>
          <div style={{ width: '80px', height: '80px', borderRadius: '50%', backgroundColor: 'var(--success-light)', display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 24px' }}>
            <Check size={40} style={{ color: 'var(--success)' }} />
          </div>
          <h2 style={{ marginBottom: '16px' }}>Team Registered!</h2>
          <p style={{ color: 'var(--text-secondary)', marginBottom: '24px' }}>
            Your team <strong>{teamName}</strong> has been successfully registered for the hackathon.
            Check your email for further instructions.
          </p>
          <Link to="/login" className="btn btn-primary">Go to Login</Link>
        </div>
      </div>
    )
  }

  return (
    <div className="auth-page">
      <div className="auth-card" style={{ maxWidth: '560px' }}>
        <div className="auth-logo"><Zap size={32} />HackCRM</div>
        <h2 className="auth-title">Register Your Team</h2>

        <div className="step-progress">
          {[1, 2, 3].map((s) => (
            <div key={s} className={`step-item ${step === s ? 'step-active' : ''} ${step > s ? 'step-complete' : ''}`}>
              <div className="step-circle">{step > s ? <Check size={16} /> : s}</div>
              <span className="step-label">{s === 1 ? 'Event' : s === 2 ? 'Team Info' : 'Members'}</span>
            </div>
          ))}
        </div>

        {error && (
          <div style={{ marginBottom: '16px' }}>
            <Alert type="error" message={error} onClose={() => setError('')} />
          </div>
        )}

        {step === 1 && (
          <div>
            <div className="form-group">
              <label className="form-label">
                <Calendar size={16} style={{ marginRight: '8px', verticalAlign: 'middle' }} />
                Select Hackathon Event
              </label>
              <select className="form-select w-full" value={selectedEvent} onChange={(e) => setSelectedEvent(e.target.value)}>
                <option value="">Choose an event...</option>
                {activeEvents.map((event) => (
                  <option key={event.id} value={event.id}>{event.name} ({event.start_date})</option>
                ))}
              </select>
            </div>
            {selectedEvent && (() => {
              const event = activeEvents.find((e) => String(e.id) === selectedEvent)
              return event ? (
                <div style={{ padding: '16px', backgroundColor: 'var(--body-bg)', borderRadius: 'var(--radius-md)', marginTop: '16px' }}>
                  <div style={{ display: 'flex', alignItems: 'center', gap: '8px', marginBottom: '8px' }}>
                    <Info size={16} style={{ color: 'var(--primary)' }} />
                    <span style={{ fontWeight: 500 }}>Event Details</span>
                  </div>
                  <div style={{ fontSize: '14px', color: 'var(--text-secondary)' }}>
                    <p><strong>Date:</strong> {event.start_date} - {event.end_date}</p>
                    <p><strong>Location:</strong> {event.location ?? 'TBD'}</p>
                    <p><strong>Teams Registered:</strong> {event.teams_count ?? 0} / {event.max_teams}</p>
                  </div>
                </div>
              ) : null
            })()}
          </div>
        )}

        {step === 2 && (
          <div>
            <div className="form-group">
              <label className="form-label">Team Name</label>
              <input type="text" className="form-input" placeholder="Enter your team name" value={teamName} onChange={(e) => setTeamName(e.target.value)} />
            </div>
            <div className="form-group">
              <label className="form-label">Team Category</label>
              <div style={{ display: 'flex', gap: '16px', marginTop: '8px' }}>
                <label style={{ flex: 1, display: 'flex', alignItems: 'center', gap: '12px', padding: '16px', border: `2px solid ${category === 'red' ? 'var(--red-team)' : 'var(--border)'}`, borderRadius: 'var(--radius-md)', cursor: 'pointer', backgroundColor: category === 'red' ? 'rgba(220, 38, 38, 0.05)' : 'transparent' }}>
                  <input type="radio" name="category" value="red" checked={category === 'red'} onChange={() => setCategory('red')} style={{ display: 'none' }} />
                  <div style={{ width: '24px', height: '24px', borderRadius: '50%', backgroundColor: 'var(--red-team)' }} />
                  <span style={{ fontWeight: 500 }}>Red Team</span>
                </label>
                <label style={{ flex: 1, display: 'flex', alignItems: 'center', gap: '12px', padding: '16px', border: `2px solid ${category === 'blue' ? 'var(--blue-team)' : 'var(--border)'}`, borderRadius: 'var(--radius-md)', cursor: 'pointer', backgroundColor: category === 'blue' ? 'rgba(37, 99, 235, 0.05)' : 'transparent' }}>
                  <input type="radio" name="category" value="blue" checked={category === 'blue'} onChange={() => setCategory('blue')} style={{ display: 'none' }} />
                  <div style={{ width: '24px', height: '24px', borderRadius: '50%', backgroundColor: 'var(--blue-team)' }} />
                  <span style={{ fontWeight: 500 }}>Blue Team</span>
                </label>
              </div>
            </div>
          </div>
        )}

        {step === 3 && (
          <div>
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: '16px' }}>
              <label className="form-label" style={{ margin: 0 }}>
                <Users size={16} style={{ marginRight: '8px', verticalAlign: 'middle' }} />
                Team Members ({members.length}/5)
              </label>
              <button type="button" className="btn btn-secondary btn-sm" onClick={addMember} disabled={members.length >= 5}>
                <Plus size={14} /> Add Member
              </button>
            </div>
            <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
              {members.map((member, index) => (
                <div key={index} style={{ padding: '16px', border: '1px solid var(--border)', borderRadius: 'var(--radius-md)' }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '12px' }}>
                    <span style={{ fontWeight: 500, fontSize: '14px' }}>Member {index + 1}</span>
                    {members.length > 1 && (
                      <button type="button" className="btn btn-ghost btn-sm" onClick={() => removeMember(index)} style={{ color: 'var(--error)' }}>
                        <Trash2 size={14} />
                      </button>
                    )}
                  </div>
                  <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '12px' }}>
                    <input type="text" className="form-input" placeholder="Name" value={member.name} onChange={(e) => updateMember(index, 'name', e.target.value)} />
                    <input type="email" className="form-input" placeholder="Email" value={member.email} onChange={(e) => updateMember(index, 'email', e.target.value)} />
                  </div>
                  <select className="form-select w-full" style={{ marginTop: '12px' }} value={member.role} onChange={(e) => updateMember(index, 'role', e.target.value)}>
                    <option value="Team Lead">Team Lead</option>
                    <option value="Developer">Developer</option>
                    <option value="Designer">Designer</option>
                    <option value="Product Manager">Product Manager</option>
                  </select>
                </div>
              ))}
            </div>
          </div>
        )}

        <div style={{ display: 'flex', gap: '12px', marginTop: '24px' }}>
          {step > 1 && (
            <button type="button" className="btn btn-secondary" onClick={() => setStep(step - 1)}>Back</button>
          )}
          {step < 3 ? (
            <button type="button" className="btn btn-primary" style={{ marginLeft: 'auto' }} onClick={() => setStep(step + 1)} disabled={!canProceed()}>Continue</button>
          ) : (
            <button type="button" className="btn btn-primary" style={{ marginLeft: 'auto' }} onClick={handleSubmit} disabled={!canProceed() || loading}>
              {loading ? (<><Loader2 size={18} style={{ animation: 'spin 1s linear infinite' }} />Registering...</>) : 'Register Team'}
            </button>
          )}
        </div>

        <p className="auth-footer">Already have an account? <Link to="/login">Sign in</Link></p>
      </div>
      <style>{`@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }`}</style>
    </div>
  )
}
