import { ReactNode } from 'react'
import { Link } from 'react-router-dom'
import { ChevronRight } from 'lucide-react'

interface StatCardProps {
  icon: ReactNode
  label: string
  value: number | string
  color: 'blue' | 'red' | 'green' | 'yellow'
  linkTo?: string
}

export function StatCard({ icon, label, value, color, linkTo }: StatCardProps) {
  return (
    <div className={`stat-card stat-${color}`}>
      <div className={`stat-card-icon icon-${color}`}>{icon}</div>
      <div className="stat-card-value">{value}</div>
      <div className="stat-card-label">{label}</div>
      {linkTo && (
        <Link to={linkTo} className="stat-card-link">
          View all <ChevronRight size={14} />
        </Link>
      )}
    </div>
  )
}
