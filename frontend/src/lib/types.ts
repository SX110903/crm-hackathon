export interface User {
  id: number
  name: string
  email: string
  role: 'admin' | 'manager'
  is_active: boolean
}

export interface Event {
  id: number
  name: string
  description: string | null
  start_date: string
  end_date: string
  location: string | null
  max_teams: number
  category_type: 'general' | 'red_vs_blue' | 'mixed'
  status: 'upcoming' | 'active' | 'closed'
  teams_count?: number
}

export interface Team {
  id: number
  name: string
  category: 'red' | 'blue' | 'general'
  event_id: number | null
  max_members: number
  leader_id: number | null
  score?: number
}

export interface Participant {
  id: number
  first_name: string
  last_name: string
  email: string
  phone: string | null
  university: string | null
  major: string | null
  year_of_study: number | null
}

export interface Project {
  id: number
  team_id: number
  name: string
  description: string | null
  category: string | null
  technology_stack: string | null
  github_url: string | null
  demo_url: string | null
  status: 'In Progress' | 'Submitted' | 'Under Review' | 'Evaluated' | 'Awarded'
}

export interface Judge {
  id: number
  first_name: string
  last_name: string
  email: string
  company: string | null
  expertise: string | null
  years_of_experience: number | null
}

export interface Mentor {
  id: number
  first_name: string
  last_name: string
  email: string
  company: string | null
  specialization: string | null
  available_slots: number | null
}

export interface Evaluation {
  id: number
  project_id: number
  judge_id: number
  innovation_score: number
  technical_score: number
  presentation_score: number
  usability_score: number
  total_score: number
  comments: string | null
  created_at: string
}

export interface TeamMember {
  id: number
  team_id: number
  participant_id: number
  role: string
  participant: Participant
  created_at: string
  updated_at: string
}

export interface Award {
  id: number
  name: string
  category: string | null
  prize: string | null
  project_id: number | null
  awarded_date: string | null
}

export interface DashboardStats {
  participants: number
  teams: number
  projects: number
  judges: number
  mentors: number
  evaluations: number
  awards: number
  red_team_score: number
  blue_team_score: number
  top_projects: Array<{
    rank: number
    project_name: string
    team_name: string
    score: number
  }>
  upcoming_events: Array<{
    id: number
    name: string
    start_date: string
    status: string
  }>
  recent_activity: Array<{
    id: number
    type: string
    message: string
    timestamp: string
  }>
}
