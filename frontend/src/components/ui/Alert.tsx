import { ReactNode } from 'react'
import { CheckCircle, AlertCircle, AlertTriangle, X } from 'lucide-react'

interface AlertProps {
  type: 'success' | 'error' | 'warning'
  message: string
  onClose?: () => void
}

export function Alert({ type, message, onClose }: AlertProps) {
  const icons: Record<string, ReactNode> = {
    success: <CheckCircle size={20} />,
    error: <AlertCircle size={20} />,
    warning: <AlertTriangle size={20} />,
  }

  return (
    <div className={`alert alert-${type}`}>
      {icons[type]}
      <span style={{ flex: 1 }}>{message}</span>
      {onClose && (
        <button onClick={onClose} style={{ background: 'none', border: 'none', cursor: 'pointer', color: 'inherit' }}>
          <X size={18} />
        </button>
      )}
    </div>
  )
}
