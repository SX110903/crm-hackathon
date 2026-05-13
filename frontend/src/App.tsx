import { Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider, useAuth } from '@/lib/auth'

// Public pages
import { Landing } from '@/pages/Landing'
import { Login } from '@/pages/Login'
import { Register } from '@/pages/Register'
import { LiveBattle } from '@/pages/LiveBattle'

// Admin pages
import { Layout } from '@/components/Layout'
import { Dashboard } from '@/pages/Dashboard'
import { Events } from '@/pages/Events'
import { Participants } from '@/pages/Participants'
import { Teams } from '@/pages/Teams'
import { RedTeam } from '@/pages/RedTeam'
import { BlueTeam } from '@/pages/BlueTeam'
import { Projects } from '@/pages/Projects'
import { Judges } from '@/pages/Judges'
import { Mentors } from '@/pages/Mentors'
import { Evaluations } from '@/pages/Evaluations'
import { Awards } from '@/pages/Awards'
import { Settings } from '@/pages/Settings'
import { TeamAssignment } from '@/pages/TeamAssignment'

function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const { isAuthenticated } = useAuth()
  
  if (!isAuthenticated) {
    return <Navigate to="/login" replace />
  }
  
  return <>{children}</>
}

function AppRoutes() {
  return (
    <Routes>
      {/* Public routes */}
      <Route path="/" element={<Landing />} />
      <Route path="/login" element={<Login />} />
      <Route path="/register" element={<Register />} />
      <Route path="/live-battle" element={<LiveBattle />} />

      {/* Protected admin routes */}
      <Route
        element={
          <ProtectedRoute>
            <Layout />
          </ProtectedRoute>
        }
      >
        <Route path="/dashboard" element={<Dashboard />} />
        <Route path="/events" element={<Events />} />
        <Route path="/participants" element={<Participants />} />
        <Route path="/teams" element={<Teams />} />
        <Route path="/red-team" element={<RedTeam />} />
        <Route path="/blue-team" element={<BlueTeam />} />
        <Route path="/projects" element={<Projects />} />
        <Route path="/judges" element={<Judges />} />
        <Route path="/mentors" element={<Mentors />} />
        <Route path="/evaluations" element={<Evaluations />} />
        <Route path="/awards" element={<Awards />} />
        <Route path="/team-assignment" element={<TeamAssignment />} />
        <Route path="/settings" element={<Settings />} />
      </Route>

      {/* Fallback */}
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  )
}

function App() {
  return (
    <AuthProvider>
      <AppRoutes />
    </AuthProvider>
  )
}

export default App
