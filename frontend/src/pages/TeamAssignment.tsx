import { useState } from 'react'
import { UserMinus, UserPlus, Users, Search, CircleDot } from 'lucide-react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { teamsApi, participantsApi } from '@/lib/api'
import type { Team, Participant, TeamMember } from '@/lib/types'

export function TeamAssignment() {
  const queryClient = useQueryClient()
  const [selectedRedId, setSelectedRedId] = useState<number | null>(null)
  const [selectedBlueId, setSelectedBlueId] = useState<number | null>(null)
  const [search, setSearch] = useState('')
  const [error, setError] = useState<string | null>(null)

  const { data: redTeams = [], isLoading: redTeamsLoading } = useQuery<Team[]>({
    queryKey: ['teams', 'red'],
    queryFn: () => teamsApi.getAll({ category: 'red', per_page: 100 } as any).then(r => r.data.data),
  })

  const { data: blueTeams = [], isLoading: blueTeamsLoading } = useQuery<Team[]>({
    queryKey: ['teams', 'blue'],
    queryFn: () => teamsApi.getAll({ category: 'blue', per_page: 100 } as any).then(r => r.data.data),
  })

  const activeRedId = selectedRedId ?? redTeams[0]?.id ?? null
  const activeBlueId = selectedBlueId ?? blueTeams[0]?.id ?? null

  const activeRedTeam = redTeams.find(t => t.id === activeRedId) ?? null
  const activeBlueTeam = blueTeams.find(t => t.id === activeBlueId) ?? null

  const { data: redMembers = [], isLoading: redMembersLoading } = useQuery<TeamMember[]>({
    queryKey: ['team-members', activeRedId],
    queryFn: () => teamsApi.getMembers(activeRedId!).then(r => r.data.data),
    enabled: activeRedId !== null,
  })

  const { data: blueMembers = [], isLoading: blueMembersLoading } = useQuery<TeamMember[]>({
    queryKey: ['team-members', activeBlueId],
    queryFn: () => teamsApi.getMembers(activeBlueId!).then(r => r.data.data),
    enabled: activeBlueId !== null,
  })

  const { data: unassigned = [], isLoading: unassignedLoading } = useQuery<Participant[]>({
    queryKey: ['participants', 'unassigned'],
    queryFn: () =>
      participantsApi.getAll({ unassigned: true, per_page: 200 }).then(r => r.data.data),
  })

  const addMember = useMutation({
    mutationFn: ({ teamId, participantId }: { teamId: number; participantId: number }) =>
      teamsApi.addMember(teamId, { participant_id: participantId, role: 'Developer' }),
    onSuccess: (_, { teamId }) => {
      setError(null)
      queryClient.invalidateQueries({ queryKey: ['team-members', teamId] })
      queryClient.invalidateQueries({ queryKey: ['participants', 'unassigned'] })
    },
    onError: (err: any) => {
      const msg =
        err?.response?.data?.message ??
        err?.response?.data?.errors?.participant_id?.[0] ??
        'Failed to assign participant.'
      setError(msg)
    },
  })

  const removeMember = useMutation({
    mutationFn: ({ teamId, participantId }: { teamId: number; participantId: number }) =>
      teamsApi.removeMember(teamId, participantId),
    onSuccess: (_, { teamId }) => {
      setError(null)
      queryClient.invalidateQueries({ queryKey: ['team-members', teamId] })
      queryClient.invalidateQueries({ queryKey: ['participants', 'unassigned'] })
    },
    onError: () => setError('Failed to remove member.'),
  })

  const filtered = unassigned.filter(p => {
    const term = search.toLowerCase()
    return (
      p.first_name.toLowerCase().includes(term) ||
      p.last_name.toLowerCase().includes(term) ||
      p.email.toLowerCase().includes(term) ||
      (p.university ?? '').toLowerCase().includes(term)
    )
  })

  const isBusy = addMember.isPending || removeMember.isPending

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Team Assignment</h1>
      </div>

      {error && (
        <div
          style={{
            background: 'var(--error-light)',
            color: 'var(--error)',
            border: '1px solid var(--error)',
            borderRadius: 'var(--radius-md)',
            padding: '10px 16px',
            marginBottom: '20px',
            fontSize: '14px',
          }}
        >
          {error}
        </div>
      )}

      {/* Two-column layout */}
      <div style={{ display: 'flex', gap: '20px', marginBottom: '24px' }}>
        {/* RED TEAM COLUMN */}
        <div style={{ flex: 1 }}>
          <div
            className="card"
            style={{ borderTop: '3px solid var(--red-team)' }}
          >
            <div className="card-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
              <h2
                style={{
                  fontSize: '16px',
                  fontWeight: 700,
                  color: 'var(--red-team)',
                  display: 'flex',
                  alignItems: 'center',
                  gap: '8px',
                  margin: 0,
                }}
              >
                <CircleDot size={18} /> Red Team
              </h2>
              {redTeams.length > 1 && (
                <select
                  className="form-select"
                  style={{ fontSize: '13px', padding: '4px 8px' }}
                  value={activeRedId ?? ''}
                  onChange={e => setSelectedRedId(Number(e.target.value))}
                >
                  {redTeams.map(t => (
                    <option key={t.id} value={t.id}>{t.name}</option>
                  ))}
                </select>
              )}
              {redTeams.length === 1 && activeRedTeam && (
                <span style={{ fontSize: '13px', color: 'var(--text-muted)' }}>{activeRedTeam.name}</span>
              )}
            </div>

            <div className="card-body" style={{ padding: '0' }}>
              {redTeamsLoading || redMembersLoading ? (
                <div style={{ padding: '24px', color: 'var(--text-muted)', textAlign: 'center' }}>
                  Loading...
                </div>
              ) : redTeams.length === 0 ? (
                <div style={{ padding: '24px', color: 'var(--text-muted)', textAlign: 'center' }}>
                  No red teams found. Create a red team first.
                </div>
              ) : (
                <table className="table">
                  <thead>
                    <tr>
                      <th>Member</th>
                      <th>Role</th>
                      <th style={{ width: '48px' }}></th>
                    </tr>
                  </thead>
                  <tbody>
                    {redMembers.length === 0 ? (
                      <tr>
                        <td colSpan={3} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>
                          No members yet
                        </td>
                      </tr>
                    ) : (
                      redMembers.map(m => (
                        <tr key={m.id} className="table-row-red">
                          <td style={{ fontWeight: 500 }}>
                            {m.participant.first_name} {m.participant.last_name}
                          </td>
                          <td style={{ color: 'var(--text-muted)', fontSize: '13px' }}>{m.role}</td>
                          <td>
                            <button
                              className="btn btn-ghost btn-sm"
                              style={{ color: 'var(--error)' }}
                              disabled={isBusy}
                              title="Remove from team"
                              onClick={() =>
                                removeMember.mutate({
                                  teamId: activeRedId!,
                                  participantId: m.participant_id,
                                })
                              }
                            >
                              <UserMinus size={14} />
                            </button>
                          </td>
                        </tr>
                      ))
                    )}
                  </tbody>
                </table>
              )}
            </div>

            {activeRedTeam && (
              <div className="card-footer" style={{ fontSize: '12px', color: 'var(--text-muted)' }}>
                {redMembers.length} / {activeRedTeam.max_members} members
              </div>
            )}
          </div>
        </div>

        {/* BLUE TEAM COLUMN */}
        <div style={{ flex: 1 }}>
          <div
            className="card"
            style={{ borderTop: '3px solid var(--blue-team)' }}
          >
            <div className="card-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
              <h2
                style={{
                  fontSize: '16px',
                  fontWeight: 700,
                  color: 'var(--blue-team)',
                  display: 'flex',
                  alignItems: 'center',
                  gap: '8px',
                  margin: 0,
                }}
              >
                <CircleDot size={18} /> Blue Team
              </h2>
              {blueTeams.length > 1 && (
                <select
                  className="form-select"
                  style={{ fontSize: '13px', padding: '4px 8px' }}
                  value={activeBlueId ?? ''}
                  onChange={e => setSelectedBlueId(Number(e.target.value))}
                >
                  {blueTeams.map(t => (
                    <option key={t.id} value={t.id}>{t.name}</option>
                  ))}
                </select>
              )}
              {blueTeams.length === 1 && activeBlueTeam && (
                <span style={{ fontSize: '13px', color: 'var(--text-muted)' }}>{activeBlueTeam.name}</span>
              )}
            </div>

            <div className="card-body" style={{ padding: '0' }}>
              {blueTeamsLoading || blueMembersLoading ? (
                <div style={{ padding: '24px', color: 'var(--text-muted)', textAlign: 'center' }}>
                  Loading...
                </div>
              ) : blueTeams.length === 0 ? (
                <div style={{ padding: '24px', color: 'var(--text-muted)', textAlign: 'center' }}>
                  No blue teams found. Create a blue team first.
                </div>
              ) : (
                <table className="table">
                  <thead>
                    <tr>
                      <th>Member</th>
                      <th>Role</th>
                      <th style={{ width: '48px' }}></th>
                    </tr>
                  </thead>
                  <tbody>
                    {blueMembers.length === 0 ? (
                      <tr>
                        <td colSpan={3} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>
                          No members yet
                        </td>
                      </tr>
                    ) : (
                      blueMembers.map(m => (
                        <tr key={m.id} className="table-row-blue">
                          <td style={{ fontWeight: 500 }}>
                            {m.participant.first_name} {m.participant.last_name}
                          </td>
                          <td style={{ color: 'var(--text-muted)', fontSize: '13px' }}>{m.role}</td>
                          <td>
                            <button
                              className="btn btn-ghost btn-sm"
                              style={{ color: 'var(--error)' }}
                              disabled={isBusy}
                              title="Remove from team"
                              onClick={() =>
                                removeMember.mutate({
                                  teamId: activeBlueId!,
                                  participantId: m.participant_id,
                                })
                              }
                            >
                              <UserMinus size={14} />
                            </button>
                          </td>
                        </tr>
                      ))
                    )}
                  </tbody>
                </table>
              )}
            </div>

            {activeBlueTeam && (
              <div className="card-footer" style={{ fontSize: '12px', color: 'var(--text-muted)' }}>
                {blueMembers.length} / {activeBlueTeam.max_members} members
              </div>
            )}
          </div>
        </div>
      </div>

      {/* UNASSIGNED PARTICIPANTS PANEL */}
      <div className="card">
        <div className="card-header" style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
          <h2 style={{ fontSize: '16px', fontWeight: 700, margin: 0, display: 'flex', alignItems: 'center', gap: '8px' }}>
            <Users size={18} /> Unassigned Participants
            {!unassignedLoading && (
              <span
                style={{
                  fontSize: '12px',
                  fontWeight: 400,
                  background: 'var(--body-bg)',
                  border: '1px solid var(--border)',
                  borderRadius: 'var(--radius-full)',
                  padding: '2px 8px',
                  color: 'var(--text-muted)',
                }}
              >
                {unassigned.length}
              </span>
            )}
          </h2>
          <div style={{ position: 'relative' }}>
            <Search
              size={14}
              style={{
                position: 'absolute',
                left: '10px',
                top: '50%',
                transform: 'translateY(-50%)',
                color: 'var(--text-muted)',
                pointerEvents: 'none',
              }}
            />
            <input
              type="text"
              className="form-input"
              style={{ paddingLeft: '30px', fontSize: '13px', padding: '6px 10px 6px 30px' }}
              placeholder="Search participants..."
              value={search}
              onChange={e => setSearch(e.target.value)}
            />
          </div>
        </div>

        <div style={{ padding: '0' }}>
          <table className="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>University</th>
                <th style={{ width: '180px' }}>Assign to</th>
              </tr>
            </thead>
            <tbody>
              {unassignedLoading ? (
                <tr>
                  <td colSpan={4} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>
                    Loading...
                  </td>
                </tr>
              ) : filtered.length === 0 ? (
                <tr>
                  <td colSpan={4} style={{ textAlign: 'center', color: 'var(--text-muted)' }}>
                    {search ? 'No participants match your search.' : 'All participants are assigned to teams.'}
                  </td>
                </tr>
              ) : (
                filtered.map(p => (
                  <tr key={p.id}>
                    <td style={{ fontWeight: 500 }}>
                      {p.first_name} {p.last_name}
                    </td>
                    <td style={{ color: 'var(--text-muted)', fontSize: '13px' }}>{p.email}</td>
                    <td style={{ color: 'var(--text-muted)', fontSize: '13px' }}>{p.university ?? '-'}</td>
                    <td>
                      <div className="action-buttons">
                        <button
                          className="btn btn-sm"
                          style={{
                            color: 'var(--red-team)',
                            border: '1px solid var(--red-team)',
                            background: 'transparent',
                          }}
                          disabled={isBusy || activeRedId === null}
                          title={activeRedId === null ? 'No red team available' : `Assign to ${activeRedTeam?.name}`}
                          onClick={() =>
                            addMember.mutate({ teamId: activeRedId!, participantId: p.id })
                          }
                        >
                          <UserPlus size={12} /> Red
                        </button>
                        <button
                          className="btn btn-sm"
                          style={{
                            color: 'var(--blue-team)',
                            border: '1px solid var(--blue-team)',
                            background: 'transparent',
                          }}
                          disabled={isBusy || activeBlueId === null}
                          title={activeBlueId === null ? 'No blue team available' : `Assign to ${activeBlueTeam?.name}`}
                          onClick={() =>
                            addMember.mutate({ teamId: activeBlueId!, participantId: p.id })
                          }
                        >
                          <UserPlus size={12} /> Blue
                        </button>
                      </div>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  )
}
