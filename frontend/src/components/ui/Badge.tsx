import { ReactNode } from 'react'

interface BadgeProps {
  children: ReactNode
  variant: 'primary' | 'success' | 'warning' | 'error' | 'gray' | 'gold'
}

export function Badge({ children, variant }: BadgeProps) {
  return <span className={`badge badge-${variant}`}>{children}</span>
}

export function getStatusBadge(status: string) {
  switch (status) {
    case 'active':
      return <Badge variant="success">Active</Badge>
    case 'upcoming':
      return <Badge variant="warning">Upcoming</Badge>
    case 'closed':
      return <Badge variant="gray">Closed</Badge>
    case 'in_progress':
      return <Badge variant="warning">In Progress</Badge>
    case 'submitted':
      return <Badge variant="primary">Submitted</Badge>
    case 'evaluated':
      return <Badge variant="success">Evaluated</Badge>
    case 'awarded':
      return <Badge variant="gold">Awarded</Badge>
    case 'registered':
      return <Badge variant="gray">Registered</Badge>
    default:
      return <Badge variant="gray">{status}</Badge>
  }
}

export function getScoreBadge(score: number) {
  if (score >= 8) return <Badge variant="success">{score.toFixed(1)}</Badge>
  if (score >= 6) return <Badge variant="warning">{score.toFixed(1)}</Badge>
  return <Badge variant="error">{score.toFixed(1)}</Badge>
}
