import { useState } from 'react'
import { Save, Bell, Lock, Palette, Globe } from 'lucide-react'
import { Alert } from '@/components/ui/Alert'

export function Settings() {
  const [saved, setSaved] = useState(false)
  const [settings, setSettings] = useState({
    siteName: 'HackCRM',
    timezone: 'America/Los_Angeles',
    emailNotifications: true,
    slackNotifications: false,
    autoRefreshInterval: 10,
    theme: localStorage.getItem('theme') || 'light',
  })

  const applyTheme = (newTheme: string) => {
    document.documentElement.classList.toggle('dark', newTheme === 'dark')
    localStorage.setItem('theme', newTheme)
    setSettings((s) => ({ ...s, theme: newTheme }))
  }

  const handleSave = () => {
    setSaved(true)
    setTimeout(() => setSaved(false), 3000)
  }

  return (
    <div>
      <div className="page-header">
        <h1 className="page-title">Settings</h1>
        <button className="btn btn-primary" onClick={handleSave}>
          <Save size={18} /> Save Changes
        </button>
      </div>

      {saved && (
        <div style={{ marginBottom: '24px' }}>
          <Alert type="success" message="Settings saved successfully!" onClose={() => setSaved(false)} />
        </div>
      )}

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '24px' }}>
        {/* General Settings */}
        <div className="card">
          <div className="card-header">
            <h3 style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '16px', fontWeight: 600 }}>
              <Globe size={20} /> General Settings
            </h3>
          </div>
          <div className="card-body">
            <div className="form-group">
              <label className="form-label">Site Name</label>
              <input
                type="text"
                className="form-input"
                value={settings.siteName}
                onChange={(e) => setSettings({ ...settings, siteName: e.target.value })}
              />
            </div>
            <div className="form-group">
              <label className="form-label">Timezone</label>
              <select
                className="form-select w-full"
                value={settings.timezone}
                onChange={(e) => setSettings({ ...settings, timezone: e.target.value })}
              >
                <option value="America/Los_Angeles">Pacific Time (PT)</option>
                <option value="America/Denver">Mountain Time (MT)</option>
                <option value="America/Chicago">Central Time (CT)</option>
                <option value="America/New_York">Eastern Time (ET)</option>
                <option value="UTC">UTC</option>
              </select>
            </div>
            <div className="form-group">
              <label className="form-label">Auto-refresh Interval (seconds)</label>
              <input
                type="number"
                className="form-input"
                value={settings.autoRefreshInterval}
                onChange={(e) => setSettings({ ...settings, autoRefreshInterval: parseInt(e.target.value) })}
                min={5}
                max={60}
              />
              <span className="form-hint">How often to refresh live data (5-60 seconds)</span>
            </div>
          </div>
        </div>

        {/* Notifications */}
        <div className="card">
          <div className="card-header">
            <h3 style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '16px', fontWeight: 600 }}>
              <Bell size={20} /> Notifications
            </h3>
          </div>
          <div className="card-body">
            <div className="form-group">
              <label style={{ display: 'flex', alignItems: 'center', gap: '12px', cursor: 'pointer' }}>
                <input
                  type="checkbox"
                  checked={settings.emailNotifications}
                  onChange={(e) => setSettings({ ...settings, emailNotifications: e.target.checked })}
                  style={{ width: '18px', height: '18px' }}
                />
                <span>Email notifications</span>
              </label>
              <span className="form-hint">Receive email alerts for new registrations and evaluations</span>
            </div>
            <div className="form-group">
              <label style={{ display: 'flex', alignItems: 'center', gap: '12px', cursor: 'pointer' }}>
                <input
                  type="checkbox"
                  checked={settings.slackNotifications}
                  onChange={(e) => setSettings({ ...settings, slackNotifications: e.target.checked })}
                  style={{ width: '18px', height: '18px' }}
                />
                <span>Slack notifications</span>
              </label>
              <span className="form-hint">Send notifications to your Slack workspace</span>
            </div>
          </div>
        </div>

        {/* Appearance */}
        <div className="card">
          <div className="card-header">
            <h3 style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '16px', fontWeight: 600 }}>
              <Palette size={20} /> Appearance
            </h3>
          </div>
          <div className="card-body">
            <div className="form-group">
              <label className="form-label">Theme</label>
              <div style={{ display: 'flex', gap: '12px' }}>
                <label
                  style={{
                    flex: 1,
                    display: 'flex',
                    flexDirection: 'column',
                    alignItems: 'center',
                    gap: '8px',
                    padding: '16px',
                    border: `2px solid ${settings.theme === 'light' ? 'var(--primary)' : 'var(--border)'}`,
                    borderRadius: 'var(--radius-md)',
                    cursor: 'pointer',
                  }}
                >
                  <input
                    type="radio"
                    name="theme"
                    value="light"
                    checked={settings.theme === 'light'}
                    onChange={() => applyTheme('light')}
                    style={{ display: 'none' }}
                  />
                  <div
                    style={{
                      width: '48px',
                      height: '32px',
                      borderRadius: '4px',
                      backgroundColor: '#f8fafc',
                      border: '1px solid #e2e8f0',
                    }}
                  />
                  <span style={{ fontSize: '14px' }}>Light</span>
                </label>
                <label
                  style={{
                    flex: 1,
                    display: 'flex',
                    flexDirection: 'column',
                    alignItems: 'center',
                    gap: '8px',
                    padding: '16px',
                    border: `2px solid ${settings.theme === 'dark' ? 'var(--primary)' : 'var(--border)'}`,
                    borderRadius: 'var(--radius-md)',
                    cursor: 'pointer',
                  }}
                >
                  <input
                    type="radio"
                    name="theme"
                    value="dark"
                    checked={settings.theme === 'dark'}
                    onChange={() => applyTheme('dark')}
                    style={{ display: 'none' }}
                  />
                  <div
                    style={{
                      width: '48px',
                      height: '32px',
                      borderRadius: '4px',
                      backgroundColor: '#0f172a',
                      border: '1px solid #1e293b',
                    }}
                  />
                  <span style={{ fontSize: '14px' }}>Dark</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        {/* Security */}
        <div className="card">
          <div className="card-header">
            <h3 style={{ display: 'flex', alignItems: 'center', gap: '8px', fontSize: '16px', fontWeight: 600 }}>
              <Lock size={20} /> Security
            </h3>
          </div>
          <div className="card-body">
            <div className="form-group">
              <label className="form-label">Current Password</label>
              <input type="password" className="form-input" placeholder="Enter current password" />
            </div>
            <div className="form-group">
              <label className="form-label">New Password</label>
              <input type="password" className="form-input" placeholder="Enter new password" />
            </div>
            <div className="form-group">
              <label className="form-label">Confirm New Password</label>
              <input type="password" className="form-input" placeholder="Confirm new password" />
            </div>
            <button className="btn btn-secondary">Change Password</button>
          </div>
        </div>
      </div>
    </div>
  )
}
