<template>
  <div class="admin-settings">
    <div class="page-header">
      <div class="header-content">
        <h1><i class="fas fa-cog"></i> Settings</h1>
        <p>Configure and customize your application settings</p>
      </div>
    </div>

    <!-- Settings Navigation -->
    <div class="content-card">
      <div class="settings-nav">
        <button
          v-for="section in sections"
          :key="section.id"
          @click="activeSection = section.id"
          :class="['nav-tab', { active: activeSection === section.id }]"
        >
          <i :class="section.icon"></i>
          <span>{{ section.title }}</span>
        </button>
      </div>

      <!-- Settings Content -->
      <div class="settings-content">
        <!-- General Settings -->
        <div v-if="activeSection === 'general'" class="settings-panel">
          <div class="panel-header">
            <h3>General Configuration</h3>
            <p>Basic application settings and branding</p>
          </div>

          <div class="settings-grid">
            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-globe"></i>
                </div>
                <h4>Site Information</h4>
              </div>
              <div class="card-content">
                <div class="form-group">
                  <label for="site_name">Site Name</label>
                  <input
                    id="site_name"
                    v-model="settings.site_name"
                    type="text"
                    placeholder="Your amazing site name"
                  >
                </div>
                <div class="form-group">
                  <label for="site_description">Description</label>
                  <textarea
                    id="site_description"
                    v-model="settings.site_description"
                    placeholder="Tell the world about your site..."
                    rows="3"
                  ></textarea>
                </div>
              </div>
            </div>

            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-user-cog"></i>
                </div>
                <h4>Administrator</h4>
              </div>
              <div class="card-content">
                <div class="form-group">
                  <label for="admin_email">Admin Email</label>
                  <input
                    id="admin_email"
                    v-model="settings.admin_email"
                    type="email"
                    placeholder="admin@example.com"
                  >
                </div>
                <div class="form-group">
                  <label for="items_per_page">Items Per Page</label>
                  <select id="items_per_page" v-model="settings.items_per_page">
                    <option value="10">10 items</option>
                    <option value="15">15 items</option>
                    <option value="20">20 items</option>
                    <option value="25">25 items</option>
                    <option value="50">50 items</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Security Settings -->
        <div v-if="activeSection === 'security'" class="settings-panel">
          <div class="panel-header">
            <h3>Security & Access</h3>
            <p>Protect your application with advanced security settings</p>
          </div>

          <div class="settings-grid">
            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-shield-alt"></i>
                </div>
                <h4>Session Management</h4>
              </div>
              <div class="card-content">
                <div class="form-group">
                  <label for="session_timeout">Session Timeout (minutes)</label>
                  <input
                    id="session_timeout"
                    v-model="securitySettings.session_timeout"
                    type="number"
                    min="5"
                    max="480"
                  >
                </div>
                <div class="form-group">
                  <label for="max_login_attempts">Max Login Attempts</label>
                  <input
                    id="max_login_attempts"
                    v-model="securitySettings.max_login_attempts"
                    type="number"
                    min="3"
                    max="10"
                  >
                </div>
              </div>
            </div>

            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-key"></i>
                </div>
                <h4>Authentication</h4>
              </div>
              <div class="card-content">
                <div class="form-group checkbox-group">
                  <label>
                    <input
                      v-model="securitySettings.require_email_verification"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    Require Email Verification
                  </label>
                </div>
                <div class="form-group checkbox-group">
                  <label>
                    <input
                      v-model="securitySettings.enable_2fa"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    Enable Two-Factor Authentication
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Email Settings -->
        <div v-if="activeSection === 'email'" class="settings-panel">
          <div class="panel-header">
            <h3>Email Configuration</h3>
            <p>Configure email settings for notifications and contact forms</p>
          </div>

          <div class="settings-grid">
            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-envelope"></i>
                </div>
                <h4>SMTP Settings</h4>
              </div>
              <div class="card-content">
                <div class="form-group checkbox-group">
                  <label>
                    <input
                      v-model="emailSettings.smtp_enabled"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    Enable SMTP
                  </label>
                </div>
                <div class="form-group">
                  <label for="smtp_host">SMTP Host</label>
                  <input
                    id="smtp_host"
                    v-model="emailSettings.smtp_host"
                    type="text"
                    placeholder="smtp.gmail.com"
                  >
                </div>
                <div class="form-group">
                  <label for="smtp_port">SMTP Port</label>
                  <input
                    id="smtp_port"
                    v-model="emailSettings.smtp_port"
                    type="number"
                    placeholder="587"
                  >
                </div>
              </div>
            </div>

            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-bell"></i>
                </div>
                <h4>Notifications</h4>
              </div>
              <div class="card-content">
                <div class="form-group">
                  <label for="contact_form_emails">Contact Form Recipients</label>
                  <textarea
                    id="contact_form_emails"
                    v-model="emailSettings.contact_form_emails_text"
                    placeholder="admin@example.com, support@example.com"
                    rows="3"
                  ></textarea>
                  <small>Enter email addresses separated by commas</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- API Settings -->
        <div v-if="activeSection === 'api'" class="settings-panel">
          <div class="panel-header">
            <h3>API Configuration</h3>
            <p>Configure API rate limiting and caching</p>
          </div>

          <div class="settings-grid">
            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-tachometer-alt"></i>
                </div>
                <h4>Rate Limiting</h4>
              </div>
              <div class="card-content">
                <div class="form-group">
                  <label for="api_rate_limit">Requests per minute</label>
                  <input
                    id="api_rate_limit"
                    v-model="apiSettings.api_rate_limit"
                    type="number"
                    min="10"
                    max="1000"
                  >
                </div>
                <div class="form-group">
                  <label for="api_cache_ttl">Cache TTL (seconds)</label>
                  <input
                    id="api_cache_ttl"
                    v-model="apiSettings.api_cache_ttl"
                    type="number"
                    min="60"
                    max="86400"
                  >
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="settings-actions">
        <button @click="saveSettings" :disabled="saving" class="btn btn-primary">
          <i v-if="saving" class="fas fa-spinner fa-spin"></i>
          <i v-else class="fas fa-save"></i>
          {{ saving ? 'Saving...' : 'Save Settings' }}
        </button>

        <button @click="resetToDefaults" class="btn btn-warning">
          <i class="fas fa-undo"></i>
          Reset to Defaults
        </button>

        <button @click="loadSettings" class="btn btn-secondary">
          <i class="fas fa-sync-alt"></i>
          Reload
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { adminApiService } from '../admin-services/adminApi.js'

export default {
  name: 'AdminSettings',
  data() {
    return {
      activeSection: 'general',
      saving: false,
      sections: [
        { id: 'general', title: 'General', icon: 'fas fa-cog' },
        { id: 'security', title: 'Security', icon: 'fas fa-shield-alt' },
        { id: 'email', title: 'Email', icon: 'fas fa-envelope' },
        { id: 'api', title: 'API', icon: 'fas fa-code' }
      ],

      // Settings data
      settings: {
        site_name: '',
        site_description: '',
        admin_email: '',
        items_per_page: 15
      },

      securitySettings: {
        session_timeout: 60,
        max_login_attempts: 5,
        require_email_verification: true,
        enable_2fa: false
      },

      emailSettings: {
        smtp_enabled: false,
        smtp_host: '',
        smtp_port: 587,
        contact_form_emails: [],
        contact_form_emails_text: ''
      },

      apiSettings: {
        api_rate_limit: 100,
        api_cache_ttl: 3600
      }
    }
  },

  async mounted() {
    await this.loadSettings()
  },

  methods: {
    async loadSettings() {
      try {
        const response = await adminApiService.getSettings()
        const allSettings = response.data.data

        // Parse settings by group
        if (allSettings.general) {
          this.settings = { ...this.settings, ...this.parseSettingsGroup(allSettings.general) }
        }

        if (allSettings.security) {
          this.securitySettings = { ...this.securitySettings, ...this.parseSettingsGroup(allSettings.security) }
        }

        if (allSettings.email) {
          const emailData = this.parseSettingsGroup(allSettings.email)
          this.emailSettings = { ...this.emailSettings, ...emailData }

          // Convert array to text for contact form emails
          if (emailData.contact_form_emails) {
            this.emailSettings.contact_form_emails_text = emailData.contact_form_emails.join(', ')
          }
        }

        if (allSettings.api) {
          this.apiSettings = { ...this.apiSettings, ...this.parseSettingsGroup(allSettings.api) }
        }

      } catch (error) {
        console.error('Failed to load settings:', error)
        this.$toast?.error('Failed to load settings')
      }
    },

    parseSettingsGroup(group) {
      const parsed = {}
      group.forEach(setting => {
        parsed[setting.key] = setting.value
      })
      return parsed
    },

    async saveSettings() {
      this.saving = true

      try {
        // Prepare settings for each group
        const groups = {
          general: this.prepareSettingsForSave(this.settings, 'general'),
          security: this.prepareSettingsForSave(this.securitySettings, 'security'),
          email: this.prepareEmailSettings(),
          api: this.prepareSettingsForSave(this.apiSettings, 'api')
        }

        // Save each group
        for (const [groupName, settings] of Object.entries(groups)) {
          if (settings.length > 0) {
            await adminApiService.updateSettingsGroup(groupName, settings)
          }
        }

        this.$toast?.success('Settings saved successfully')

      } catch (error) {
        console.error('Failed to save settings:', error)
        this.$toast?.error('Failed to save settings')
      } finally {
        this.saving = false
      }
    },

    prepareSettingsForSave(settingsObject, group) {
      return Object.entries(settingsObject).map(([key, value]) => ({
        key,
        value,
        type: this.getSettingType(value),
        group,
        is_public: this.isPublicSetting(key)
      }))
    },

    prepareEmailSettings() {
      const emailData = { ...this.emailSettings }

      // Convert contact form emails text to array
      if (emailData.contact_form_emails_text) {
        emailData.contact_form_emails = emailData.contact_form_emails_text
          .split(',')
          .map(email => email.trim())
          .filter(email => email)
      }

      delete emailData.contact_form_emails_text

      return this.prepareSettingsForSave(emailData, 'email')
    },

    getSettingType(value) {
      if (typeof value === 'boolean') return 'boolean'
      if (typeof value === 'number') return 'integer'
      if (Array.isArray(value)) return 'array'
      return 'string'
    },

    isPublicSetting(key) {
      const publicSettings = ['site_name', 'site_description']
      return publicSettings.includes(key)
    },

    async resetToDefaults() {
      if (confirm('Are you sure you want to reset all settings to defaults? This cannot be undone.')) {
        try {
          await adminApiService.resetSettingsToDefaults()
          await this.loadSettings()
          this.$toast?.success('Settings reset to defaults')
        } catch (error) {
          console.error('Failed to reset settings:', error)
          this.$toast?.error('Failed to reset settings')
        }
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.admin-settings {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
  background: #f8fafc;
  min-height: 100vh;
}

// Page Header
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;

  .header-content h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 0.75rem;

    i {
      color: #3498db;
    }
  }

  .header-content p {
    color: #7f8c8d;
    margin: 0;
  }
}

// Navigation Tabs
.settings-nav {
  display: flex;
  border-bottom: 1px solid #f1f3f4;
  background: #f8f9fa;

  .nav-tab {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.25rem 2rem;
    background: none;
    border: none;
    color: #6c757d;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 3px solid transparent;
    font-size: 0.9rem;

    &:hover {
      color: #3498db;
      background: rgba(52, 152, 219, 0.05);
    }

    &.active {
      color: #3498db;
      background: white;
      border-bottom-color: #3498db;
    }

    i {
      font-size: 1rem;
    }

    @media (max-width: 768px) {
      padding: 1rem 1.25rem;

      span {
        display: none;
      }
    }
  }
}

// Content Area
.settings-content {
  padding: 2rem;
}

.settings-panel {
  .panel-header {
    margin-bottom: 2rem;
    border-bottom: 1px solid #f1f3f4;
    padding-bottom: 1rem;

    h3 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #2c3e50;
      margin: 0 0 0.5rem 0;
    }

    p {
      color: #7f8c8d;
      margin: 0;
      font-size: 0.95rem;
    }
  }
}

.settings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 2rem;

  .setting-card {
    background: #f8f9fa;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;

    &:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      border-color: #3498db;
    }

    &.full-width {
      grid-column: 1 / -1;
    }

    .card-header {
      background: white;
      padding: 1.5rem;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      gap: 1rem;

      .card-icon {
        width: 48px;
        height: 48px;
        background: #3498db;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
      }

      h4 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
      }
    }

    .card-content {
      padding: 1.5rem;

      p {
        color: #7f8c8d;
        margin: 0 0 1.5rem 0;
        line-height: 1.6;
      }

      .form-group {
        margin-bottom: 1.5rem;

        &:last-child {
          margin-bottom: 0;
        }

        label {
          display: block;
          font-weight: 600;
          color: #374151;
          margin-bottom: 0.5rem;
          font-size: 0.9rem;
        }

        input,
        textarea,
        select {
          width: 100%;
          padding: 0.75rem 1rem;
          border: 1px solid #dee2e6;
          border-radius: 8px;
          font-size: 0.9rem;
          transition: all 0.2s ease;
          background: white;
          color: #495057;

          &:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
          }

          &::placeholder {
            color: #adb5bd;
          }
        }

        textarea {
          resize: vertical;
          min-height: 80px;
        }

        select {
          cursor: pointer;
          background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
          background-position: right 1rem center;
          background-repeat: no-repeat;
          background-size: 1rem;
          appearance: none;
        }
      }

      .toggle-field {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 1.5rem;

        .toggle-info {
          flex: 1;

          h5 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0 0 0.25rem;
          }

          p {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin: 0;
          }
        }

        .toggle-switch {
          position: relative;
          display: inline-block;
          width: 50px;
          height: 28px;
          margin-left: 1rem;

          input {
            opacity: 0;
            width: 0;
            height: 0;

            &:checked + .slider {
              background-color: #3498db;

              &:before {
                transform: translateX(22px);
              }
            }
          }

          .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: 0.3s;
            border-radius: 28px;

            &:before {
              position: absolute;
              content: '';
              height: 22px;
              width: 22px;
              left: 3px;
              bottom: 3px;
              background-color: white;
              transition: 0.3s;
              border-radius: 50%;
              box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }
          }
        }
      }
    }
  }
}

// Action Button
.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.9rem;
  text-decoration: none;

  &.btn-primary {
    background: #3498db;
    color: white;

    &:hover:not(:disabled) {
      background: #2980b9;
    }

    &:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
  }

  &.btn-secondary {
    background: #6c757d;
    color: white;

    &:hover {
      background: #5a6268;
    }
  }

  &.btn-danger {
    background: #e74c3c;
    color: white;

    &:hover {
      background: #c0392b;
    }
  }
}

.settings-actions {
  padding: 1.5rem 2rem;
  background: #f8f9fa;
  border-top: 1px solid #e2e8f0;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;

  @media (max-width: 768px) {
    flex-direction: column;

    .btn {
      justify-content: center;
    }
  }
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

// Toast Notification
.toast-notification {
  position: fixed;
  top: 2rem;
  right: 2rem;
  background: white;
  border-radius: 12px;
  padding: 1rem 1.5rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  gap: 1rem;
  z-index: 1001;
  max-width: 400px;
  border: 1px solid #e2e8f0;

  &.success {
    border-left: 4px solid #27ae60;

    .toast-content i {
      color: #27ae60;
    }
  }

  &.error {
    border-left: 4px solid #e74c3c;

    .toast-content i {
      color: #e74c3c;
    }
  }

  .toast-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;

    i {
      font-size: 1.1rem;
    }

    span {
      font-weight: 500;
      color: #2c3e50;
      font-size: 0.9rem;
    }
  }

  .toast-close {
    background: none;
    border: none;
    color: #7f8c8d;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.2s ease;

    &:hover {
      background: #f8f9fa;
      color: #495057;
    }
  }
}

.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

// Responsive Design
@media (max-width: 768px) {
  .admin-settings {
    padding: 1rem;
  }

  .page-header {
    flex-direction: column;
    gap: 1rem;

    .header-content h1 {
      font-size: 1.75rem;
    }
  }

  .settings-content {
    padding: 1.5rem;
  }

  .settings-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }

  .toast-notification {
    left: 1rem;
    right: 1rem;
    top: 1rem;
    max-width: none;
  }
}

@media (max-width: 480px) {
  .setting-card .card-content {
    padding: 1rem;
  }

  .settings-nav {
    overflow-x: auto;

    .nav-tab {
      white-space: nowrap;
      min-width: fit-content;
    }
  }
}
</style>
