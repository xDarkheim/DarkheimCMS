<template>
  <div class="admin-settings">
    <div class="page-header">
      <div class="header-content">
        <h1><i class="fas fa-cog"></i> Settings</h1>
        <p>Configure and customize your application settings</p>

        <!-- Search and Quick Actions -->
        <div class="header-actions">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search settings..."
              @input="handleSearch"
            >
          </div>

          <div class="action-buttons">
            <button @click="exportSettings" class="btn btn-outline" title="Export Settings">
              <i class="fas fa-download"></i>
              Export
            </button>
            <button @click="importSettings" class="btn btn-outline" title="Import Settings">
              <i class="fas fa-upload"></i>
              Import
            </button>
            <input ref="importFile" type="file" accept=".json" @change="handleImport" style="display: none;">
          </div>
        </div>

        <!-- Unsaved Changes Warning -->
        <div v-if="hasUnsavedChanges" class="unsaved-warning">
          <i class="fas fa-exclamation-triangle"></i>
          You have unsaved changes. Don't forget to save your settings.
        </div>
      </div>
    </div>

    <!-- Settings Navigation -->
    <div class="content-card">
      <div class="settings-nav">
        <button
          v-for="section in filteredSections"
          :key="section.id"
          @click="activeSection = section.id"
          :class="['nav-tab', { active: activeSection === section.id }]"
        >
          <i :class="section.icon"></i>
          <span>{{ section.title }}</span>
          <span v-if="sectionHasMatches(section.id)" class="search-indicator">•</span>
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
                  <label for="site_name">Site Name *</label>
                  <input
                    id="site_name"
                    v-model="settings.site_name"
                    type="text"
                    placeholder="Your amazing site name"
                    :class="{ 'error': errors.site_name }"
                    @input="validateField('site_name')"
                  >
                  <span v-if="errors.site_name" class="error-text">{{ errors.site_name }}</span>
                </div>
                <div class="form-group">
                  <label for="site_description">Description</label>
                  <textarea
                    id="site_description"
                    v-model="settings.site_description"
                    placeholder="Tell the world about your site..."
                    rows="3"
                    maxlength="500"
                  ></textarea>
                  <small class="char-count">{{ settings.site_description?.length || 0 }}/500 characters</small>
                </div>
                <div class="form-group">
                  <label for="site_url">Site URL</label>
                  <input
                    id="site_url"
                    v-model="settings.site_url"
                    type="url"
                    placeholder="https://example.com"
                    :class="{ 'error': errors.site_url }"
                    @input="validateField('site_url')"
                  >
                  <span v-if="errors.site_url" class="error-text">{{ errors.site_url }}</span>
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
                  <label for="admin_email">Admin Email *</label>
                  <input
                    id="admin_email"
                    v-model="settings.admin_email"
                    type="email"
                    placeholder="admin@example.com"
                    :class="{ 'error': errors.admin_email }"
                    @input="validateField('admin_email')"
                  >
                  <span v-if="errors.admin_email" class="error-text">{{ errors.admin_email }}</span>
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
                <div class="form-group">
                  <label for="timezone">Timezone</label>
                  <select id="timezone" v-model="settings.timezone">
                    <option value="UTC">UTC</option>
                    <option value="America/New_York">Eastern Time</option>
                    <option value="America/Chicago">Central Time</option>
                    <option value="America/Denver">Mountain Time</option>
                    <option value="America/Los_Angeles">Pacific Time</option>
                    <option value="Europe/London">London</option>
                    <option value="Europe/Paris">Paris</option>
                    <option value="Asia/Tokyo">Tokyo</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-palette"></i>
                </div>
                <h4>Appearance</h4>
              </div>
              <div class="card-content">
                <div class="form-group checkbox-group">
                  <label class="modern-checkbox">
                    <input
                      v-model="settings.dark_mode"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Enable Dark Mode</span>
                  </label>
                </div>
                <div class="form-group checkbox-group">
                  <label class="modern-checkbox">
                    <input
                      v-model="settings.maintenance_mode"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Maintenance Mode</span>
                  </label>
                </div>
                <div class="form-group">
                  <label for="primary_color">Primary Color</label>
                  <div class="color-input-group">
                    <input
                      id="primary_color"
                      v-model="settings.primary_color"
                      type="color"
                      class="color-picker"
                    >
                    <input
                      v-model="settings.primary_color"
                      type="text"
                      placeholder="#3498db"
                      class="color-text"
                      @input="validateColorInput"
                    >
                  </div>
                </div>
                <div class="form-group">
                  <label for="logo_upload">Site Logo</label>
                  <div class="file-upload-area">
                    <input
                      id="logo_upload"
                      ref="logoUpload"
                      type="file"
                      accept="image/*"
                      @change="handleLogoUpload"
                      style="display: none;"
                    >
                    <div class="logo-preview" v-if="settings.site_logo">
                      <img :src="settings.site_logo" alt="Current logo" class="logo-image">
                      <button @click="removeLogo" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                    <button @click="$refs.logoUpload.click()" class="btn btn-outline">
                      <i class="fas fa-upload"></i>
                      {{ settings.site_logo ? 'Change Logo' : 'Upload Logo' }}
                    </button>
                  </div>
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
                    :class="{ 'error': errors.session_timeout }"
                    @input="validateField('session_timeout')"
                  >
                  <span v-if="errors.session_timeout" class="error-text">{{ errors.session_timeout }}</span>
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
                  <label class="modern-checkbox">
                    <input
                      v-model="securitySettings.require_email_verification"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Require Email Verification</span>
                  </label>
                </div>
                <div class="form-group checkbox-group">
                  <label class="modern-checkbox">
                    <input
                      v-model="securitySettings.enable_2fa"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Enable Two-Factor Authentication</span>
                  </label>
                </div>
                <div class="form-group checkbox-group">
                  <label class="modern-checkbox">
                    <input
                      v-model="securitySettings.force_https"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Force HTTPS</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-ban"></i>
                </div>
                <h4>IP Restrictions</h4>
              </div>
              <div class="card-content">
                <div class="form-group">
                  <label for="blocked_ips">Blocked IP Addresses</label>
                  <textarea
                    id="blocked_ips"
                    v-model="securitySettings.blocked_ips_text"
                    placeholder="192.168.1.1&#10;10.0.0.1"
                    rows="3"
                  ></textarea>
                  <small>Enter IP addresses one per line</small>
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
                  <label class="modern-checkbox">
                    <input
                      v-model="emailSettings.smtp_enabled"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Enable SMTP</span>
                  </label>
                </div>
                <div v-if="emailSettings.smtp_enabled" class="smtp-config">
                  <div class="form-group">
                    <label for="smtp_host">SMTP Host *</label>
                    <input
                      id="smtp_host"
                      v-model="emailSettings.smtp_host"
                      type="text"
                      placeholder="smtp.gmail.com"
                      :class="{ 'error': errors.smtp_host }"
                      @input="validateField('smtp_host')"
                    >
                    <span v-if="errors.smtp_host" class="error-text">{{ errors.smtp_host }}</span>
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
                  <div class="form-group">
                    <label for="smtp_username">SMTP Username</label>
                    <input
                      id="smtp_username"
                      v-model="emailSettings.smtp_username"
                      type="text"
                      placeholder="your-email@gmail.com"
                    >
                  </div>
                  <div class="form-group">
                    <label for="smtp_password">SMTP Password</label>
                    <div class="password-input">
                      <input
                        id="smtp_password"
                        v-model="emailSettings.smtp_password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="••••••••"
                      >
                      <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="password-toggle"
                      >
                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                      </button>
                    </div>
                  </div>
                  <div class="form-group">
                    <button @click="testEmail" class="btn btn-outline btn-sm">
                      <i class="fas fa-paper-plane"></i>
                      Test Email Connection
                    </button>
                  </div>
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
                <div class="form-group checkbox-group">
                  <label class="modern-checkbox">
                    <input
                      v-model="emailSettings.email_notifications"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Enable Email Notifications</span>
                  </label>
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
                <div class="form-group checkbox-group">
                  <label class="modern-checkbox">
                    <input
                      v-model="apiSettings.api_enabled"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Enable API</span>
                  </label>
                </div>
              </div>
            </div>

            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-key"></i>
                </div>
                <h4>API Keys</h4>
              </div>
              <div class="card-content">
                <div class="api-key-item">
                  <div class="api-key-info">
                    <span class="key-name">Master API Key</span>
                    <code class="api-key">{{ apiSettings.master_api_key || 'Not generated' }}</code>
                  </div>
                  <button @click="generateApiKey" class="btn btn-outline btn-sm">
                    <i class="fas fa-sync-alt"></i>
                    Regenerate
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Performance Section -->
        <div v-if="activeSection === 'performance'" class="settings-panel">
          <div class="panel-header">
            <h3>Performance & Optimization</h3>
            <p>Configure caching, compression and performance settings</p>
          </div>

          <div class="settings-grid">
            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-rocket"></i>
                </div>
                <h4>Caching</h4>
              </div>
              <div class="card-content">
                <div class="form-group checkbox-group">
                  <label class="modern-checkbox">
                    <input
                      v-model="performanceSettings.enable_caching"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Enable Page Caching</span>
                  </label>
                </div>
                <div class="form-group">
                  <label for="cache_duration">Cache Duration (hours)</label>
                  <input
                    id="cache_duration"
                    v-model="performanceSettings.cache_duration"
                    type="number"
                    min="1"
                    max="168"
                  >
                </div>
              </div>
            </div>

            <div class="setting-card">
              <div class="card-header">
                <div class="card-icon">
                  <i class="fas fa-compress"></i>
                </div>
                <h4>Compression</h4>
              </div>
              <div class="card-content">
                <div class="form-group checkbox-group">
                  <label class="modern-checkbox">
                    <input
                      v-model="performanceSettings.enable_gzip"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Enable GZIP Compression</span>
                  </label>
                </div>
                <div class="form-group checkbox-group">
                  <label class="modern-checkbox">
                    <input
                      v-model="performanceSettings.minify_css"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Minify CSS</span>
                  </label>
                </div>
                <div class="form-group checkbox-group">
                  <label class="modern-checkbox">
                    <input
                      v-model="performanceSettings.minify_js"
                      type="checkbox"
                    >
                    <span class="checkmark"></span>
                    <span class="label-text">Minify JavaScript</span>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="settings-actions">
        <button @click="saveSettings" :disabled="saving || !isFormValid" class="btn btn-primary">
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

    <!-- Toast Notifications -->
    <transition-group name="toast" tag="div" class="toast-container">
      <div
        v-for="toast in toasts"
        :key="toast.id"
        :class="['toast-notification', toast.type]"
      >
        <div class="toast-content">
          <i :class="getToastIcon(toast.type)"></i>
          <span>{{ toast.message }}</span>
        </div>
        <button @click="removeToast(toast.id)" class="toast-close">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </transition-group>
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
      searchQuery: '',
      showPassword: false,
      sections: [
        { id: 'general', title: 'General', icon: 'fas fa-cog' },
        { id: 'security', title: 'Security', icon: 'fas fa-shield-alt' },
        { id: 'email', title: 'Email', icon: 'fas fa-envelope' },
        { id: 'api', title: 'API', icon: 'fas fa-code' },
        { id: 'performance', title: 'Performance', icon: 'fas fa-rocket' }
      ],

      // Settings data
      settings: {
        site_name: '',
        site_description: '',
        admin_email: '',
        items_per_page: 15,
        site_url: '',
        timezone: 'UTC',
        dark_mode: false,
        maintenance_mode: false,
        primary_color: '#3498db',
        site_logo: null
      },

      securitySettings: {
        session_timeout: 60,
        max_login_attempts: 5,
        require_email_verification: true,
        enable_2fa: false,
        force_https: false,
        blocked_ips: [],
        blocked_ips_text: ''
      },

      emailSettings: {
        smtp_enabled: false,
        smtp_host: '',
        smtp_port: 587,
        smtp_username: '',
        smtp_password: '',
        email_notifications: true,
        contact_form_emails: [],
        contact_form_emails_text: ''
      },

      apiSettings: {
        api_rate_limit: 100,
        api_cache_ttl: 3600,
        api_enabled: false,
        master_api_key: ''
      },

      performanceSettings: {
        enable_caching: false,
        cache_duration: 24,
        enable_gzip: false,
        minify_css: false,
        minify_js: false
      },

      // Form validation and UI state
      errors: {},
      toasts: [],
      originalSettings: {},
      searchResults: []
    }
  },

  computed: {
    filteredSections() {
      if (!this.searchQuery) {
        return this.sections
      }

      const query = this.searchQuery.toLowerCase()
      return this.sections.filter(section => {
        return section.title.toLowerCase().includes(query) || this.sectionHasMatches(section.id)
      })
    },

    isFormValid() {
      const hasErrors = Object.keys(this.errors).length > 0
      const requiredFields = this.settings.site_name && this.settings.admin_email

      // Исправляем логику валидации email - если email есть, проверяем его валидность
      const validEmail = this.settings.admin_email ? this.validateEmail(this.settings.admin_email) : true

      // Если нет обязательных полей, форма все равно может быть валидной для сохранения других настроек
      return !hasErrors && validEmail
    },

    hasUnsavedChanges() {
      return JSON.stringify(this.getCurrentSettings()) !== JSON.stringify(this.originalSettings)
    }
  },

  watch: {
    settings: {
      handler() {
        this.validateAllFields()
      },
      deep: true
    },
    securitySettings: {
      handler() {
        this.validateAllFields()
      },
      deep: true
    },
    emailSettings: {
      handler() {
        this.validateAllFields()
      },
      deep: true
    }
  },

  async mounted() {
    await this.loadSettings()
    this.storeOriginalSettings()

    // Auto-hide toasts after 5 seconds
    setInterval(() => {
      this.toasts = this.toasts.filter(toast => {
        return Date.now() - toast.timestamp < 5000
      })
    }, 1000)
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
          const securityData = this.parseSettingsGroup(allSettings.security)
          this.securitySettings = { ...this.securitySettings, ...securityData }

          // Convert blocked IPs array to text
          if (securityData.blocked_ips) {
            this.securitySettings.blocked_ips_text = securityData.blocked_ips.join('\n')
          }
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

        if (allSettings.performance) {
          this.performanceSettings = { ...this.performanceSettings, ...this.parseSettingsGroup(allSettings.performance) }
        }

        this.storeOriginalSettings()

      } catch (error) {
        console.error('Failed to load settings:', error)
        this.showToast('Failed to load settings', 'error')
      }
    },

    parseSettingsGroup(group) {
      const parsed = {}
      group.forEach(setting => {
        // Handle different data types
        let value = setting.value
        if (setting.type === 'boolean') {
          value = value === 'true' || value === true
        } else if (setting.type === 'integer') {
          value = parseInt(value)
        } else if (setting.type === 'array') {
          value = Array.isArray(value) ? value : JSON.parse(value || '[]')
        }
        parsed[setting.key] = value
      })
      return parsed
    },

    async saveSettings() {
      // Добавляем дополнительное логирование для отладки
      console.log('Save Settings clicked:', {
        isFormValid: this.isFormValid,
        hasErrors: Object.keys(this.errors).length > 0,
        errors: this.errors,
        saving: this.saving
      })

      if (!this.isFormValid) {
        console.warn('Form validation failed:', this.errors)
        this.showToast('Please fix validation errors before saving', 'error')
        return
      }

      this.saving = true

      try {
        // Prepare settings for each group
        const groups = {
          general: this.prepareSettingsForSave(this.settings, 'general'),
          security: this.prepareSecuritySettings(),
          email: this.prepareEmailSettings(),
          api: this.prepareSettingsForSave(this.apiSettings, 'api'),
          performance: this.prepareSettingsForSave(this.performanceSettings, 'performance')
        }

        console.log('Prepared settings groups:', groups)

        // Save each group
        for (const [groupName, settingsArray] of Object.entries(groups)) {
          if (settingsArray.length > 0) {
            console.log(`Saving ${groupName} settings:`, settingsArray)

            // Pass the settings array directly - the API service will wrap it
            console.log(`Request data for ${groupName}:`, settingsArray)
            await adminApiService.updateSettingsGroup(groupName, settingsArray)
            console.log(`Successfully saved ${groupName} settings`)
          }
        }

        this.showToast('Settings saved successfully', 'success')
        this.storeOriginalSettings()

      } catch (error) {
        console.error('Failed to save settings:', error)

        // Более детальная информация об ошибке
        if (error.response) {
          console.error('Error response:', {
            status: error.response.status,
            statusText: error.response.statusText,
            data: error.response.data
          })

          // Показываем более конкретную ошибку пользователю
          const errorMessage = error.response.data?.message || 'Failed to save settings'
          this.showToast(errorMessage, 'error')
        } else if (error.request) {
          console.error('Network error:', error.request)
          this.showToast('Network error: Please check your connection', 'error')
        } else {
          console.error('Unexpected error:', error.message)
          this.showToast('Unexpected error occurred', 'error')
        }
      } finally {
        this.saving = false
      }
    },

    prepareSettingsForSave(settingsObject, group) {
      return Object.entries(settingsObject)
        .filter(([key, value]) => {
          // Only include settings that have a valid value
          return value !== null && value !== undefined && value !== '';
        })
        .map(([key, value]) => ({
          key,
          value: this.normalizeSettingValue(value),
          type: this.getSettingType(value),
          group,
          is_public: this.isPublicSetting(key)
        }))
    },

    normalizeSettingValue(value) {
      // Ensure arrays are properly serialized as JSON strings for storage
      if (Array.isArray(value)) {
        return JSON.stringify(value);
      }

      // Convert boolean to string for consistent storage
      if (typeof value === 'boolean') {
        return value.toString();
      }

      // Convert numbers to strings
      if (typeof value === 'number') {
        return value.toString();
      }

      // Return string values as-is
      return value;
    },

    prepareSecuritySettings() {
      const securityData = { ...this.securitySettings }

      // Convert blocked IPs text to array
      if (securityData.blocked_ips_text) {
        securityData.blocked_ips = securityData.blocked_ips_text
          .split('\n')
          .map(ip => ip.trim())
          .filter(ip => ip)
      }

      delete securityData.blocked_ips_text
      return this.prepareSettingsForSave(securityData, 'security')
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
      const publicSettings = ['site_name', 'site_description', 'primary_color', 'timezone']
      return publicSettings.includes(key)
    },

    async resetToDefaults() {
      if (confirm('Are you sure you want to reset all settings to defaults? This cannot be undone.')) {
        try {
          await adminApiService.resetSettingsToDefaults()
          await this.loadSettings()
          this.showToast('Settings reset to defaults', 'success')
        } catch (error) {
          console.error('Failed to reset settings:', error)
          this.showToast('Failed to reset settings', 'error')
        }
      }
    },

    // Validation methods
    validateField(field) {
      this.$nextTick(() => {
        if (field === 'site_name') {
          if (!this.settings.site_name || this.settings.site_name.trim().length < 2) {
            // Vue 3 compatible way to set reactive errors
            this.errors = { ...this.errors, site_name: 'Site name must be at least 2 characters' }
          } else {
            // Vue 3 compatible way to delete from reactive errors
            const { site_name, ...rest } = this.errors
            this.errors = rest
          }
        }

        if (field === 'admin_email') {
          if (!this.settings.admin_email) {
            this.errors = { ...this.errors, admin_email: 'Admin email is required' }
          } else if (!this.validateEmail(this.settings.admin_email)) {
            this.errors = { ...this.errors, admin_email: 'Please enter a valid email address' }
          } else {
            const { admin_email, ...rest } = this.errors
            this.errors = rest
          }
        }

        if (field === 'site_url' && this.settings.site_url) {
          const urlPattern = /^(ftp|http|https):\/\/[^ "]+$/
          if (!urlPattern.test(this.settings.site_url)) {
            this.errors = { ...this.errors, site_url: 'Please enter a valid URL (e.g., https://example.com)' }
          } else {
            const { site_url, ...rest } = this.errors
            this.errors = rest
          }
        }

        if (field === 'session_timeout') {
          const timeout = parseInt(this.securitySettings.session_timeout)
          if (isNaN(timeout) || timeout < 5 || timeout > 480) {
            this.errors = { ...this.errors, session_timeout: 'Session timeout must be between 5 and 480 minutes' }
          } else {
            const { session_timeout, ...rest } = this.errors
            this.errors = rest
          }
        }

        if (field === 'smtp_host' && this.emailSettings.smtp_enabled) {
          if (!this.emailSettings.smtp_host || this.emailSettings.smtp_host.trim().length < 3) {
            this.errors = { ...this.errors, smtp_host: 'SMTP host is required when SMTP is enabled' }
          } else {
            const { smtp_host, ...rest } = this.errors
            this.errors = rest
          }
        }
      })
    },

    validateAllFields() {
      this.validateField('site_name')
      this.validateField('admin_email')
      this.validateField('site_url')
      this.validateField('session_timeout')
      this.validateField('smtp_host')
    },

    validateEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      return emailRegex.test(email)
    },

    validateColorInput() {
      const colorRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/
      if (this.settings.primary_color && !colorRegex.test(this.settings.primary_color)) {
        this.errors = { ...this.errors, primary_color: 'Please enter a valid hex color (e.g., #3498db)' }
      } else {
        const { primary_color, ...rest } = this.errors
        this.errors = rest
      }
    },

    async handleLogoUpload(event) {
      const file = event.target.files[0]
      if (!file) return

      // Validate file type
      if (!file.type.startsWith('image/')) {
        this.showToast('Please select a valid image file', 'error')
        return
      }

      // Validate file size (max 2MB)
      if (file.size > 2 * 1024 * 1024) {
        this.showToast('Image size must be less than 2MB', 'error')
        return
      }

      try {
        const formData = new FormData()
        formData.append('logo', file)

        this.showToast('Uploading logo...', 'info')

        const response = await adminApiService.uploadLogo(formData)
        this.settings.site_logo = response.data.logo_url

        this.showToast('Logo uploaded successfully', 'success')
      } catch (error) {
        console.error('Failed to upload logo:', error)
        this.showToast('Failed to upload logo', 'error')
      }
    },

    removeLogo() {
      if (confirm('Are you sure you want to remove the current logo?')) {
        this.settings.site_logo = null
        this.showToast('Logo removed', 'success')
      }
    },

    // Import/Export functionality
    async exportSettings() {
      try {
        const allSettings = this.getCurrentSettings()
        const exportData = {
          timestamp: new Date().toISOString(),
          version: '1.0',
          settings: allSettings
        }

        const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' })
        const url = URL.createObjectURL(blob)
        const link = document.createElement('a')

        link.href = url
        link.download = `settings-export-${new Date().toISOString().split('T')[0]}.json`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        URL.revokeObjectURL(url)

        this.showToast('Settings exported successfully', 'success')

      } catch (error) {
        console.error('Failed to export settings:', error)
        this.showToast('Failed to export settings', 'error')
      }
    },

    importSettings() {
      this.$refs.importFile.click()
    },

    async handleImport(event) {
      const file = event.target.files[0]
      if (!file) return

      const reader = new FileReader()
      reader.onload = async (e) => {
        try {
          const importData = JSON.parse(e.target.result)

          // Validate import structure
          if (!importData.settings) {
            this.showToast('Invalid settings file format', 'error')
            return
          }

          if (!confirm('This will overwrite your current settings. Are you sure you want to continue?')) {
            return
          }

          // Apply imported settings
          const { settings } = importData
          if (settings.general) Object.assign(this.settings, settings.general)
          if (settings.security) Object.assign(this.securitySettings, settings.security)
          if (settings.email) Object.assign(this.emailSettings, settings.email)
          if (settings.api) Object.assign(this.apiSettings, settings.api)
          if (settings.performance) Object.assign(this.performanceSettings, settings.performance)

          this.showToast('Settings imported successfully', 'success')

        } catch (error) {
          console.error('Failed to import settings:', error)
          this.showToast('Invalid JSON file or corrupted data', 'error')
        }
      }

      reader.readAsText(file)
      event.target.value = ''
    },

    // Search functionality
    handleSearch() {
      if (!this.searchQuery) {
        this.searchResults = []
        return
      }

      const query = this.searchQuery.toLowerCase()
      this.searchResults = []

      // Search through all settings
      const allSettings = {
        general: this.settings,
        security: this.securitySettings,
        email: this.emailSettings,
        api: this.apiSettings,
        performance: this.performanceSettings
      }

      Object.entries(allSettings).forEach(([section, settings]) => {
        Object.keys(settings).forEach(key => {
          if (key.toLowerCase().includes(query)) {
            this.searchResults.push({ section, key })
          }
        })
      })

      // Auto-switch to first matching section
      if (this.searchResults.length > 0) {
        this.activeSection = this.searchResults[0].section
      }
    },

    sectionHasMatches(sectionId) {
      return this.searchResults.some(result => result.section === sectionId)
    },

    // API testing functionality
    async testEmail() {
      try {
        this.showToast('Testing email connection...', 'info')

        const testData = {
          smtp_host: this.emailSettings.smtp_host,
          smtp_port: this.emailSettings.smtp_port,
          smtp_username: this.emailSettings.smtp_username,
          smtp_password: this.emailSettings.smtp_password
        }

        await adminApiService.testEmailConnection(testData)
        this.showToast('Email connection test successful!', 'success')

      } catch (error) {
        console.error('Email test failed:', error)
        this.showToast('Email connection test failed. Please check your settings.', 'error')
      }
    },

    async generateApiKey() {
      try {
        const response = await adminApiService.generateApiKey()
        this.apiSettings.master_api_key = response.data.api_key
        this.showToast('New API key generated successfully', 'success')

      } catch (error) {
        console.error('Failed to generate API key:', error)
        this.showToast('Failed to generate API key', 'error')
      }
    },

    // Utility methods
    getCurrentSettings() {
      return {
        general: this.settings,
        security: { ...this.securitySettings },
        email: { ...this.emailSettings },
        api: this.apiSettings,
        performance: this.performanceSettings
      }
    },

    storeOriginalSettings() {
      this.originalSettings = JSON.parse(JSON.stringify(this.getCurrentSettings()))
    },

    // Toast notification system
    showToast(message, type = 'info') {
      const toast = {
        id: Date.now() + Math.random(),
        message,
        type,
        timestamp: Date.now()
      }

      this.toasts.push(toast)

      // Auto-remove after 5 seconds
      setTimeout(() => {
        this.removeToast(toast.id)
      }, 5000)
    },

    removeToast(id) {
      this.toasts = this.toasts.filter(toast => toast.id !== id)
    },

    getToastIcon(type) {
      const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
      }
      return icons[type] || icons.info
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

.page-header {
  margin-bottom: 2rem;

  .header-content {
    h1 {
      font-size: 2rem;
      font-weight: 700;
      color: #1a365d;
      margin: 0 0 0.5rem 0;
      display: flex;
      align-items: center;
      gap: 0.75rem;

      i {
        color: #3182ce;
      }
    }

    p {
      color: #4a5568;
      margin: 0 0 1.5rem 0;
      font-size: 1rem;
    }
  }
}

.header-actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;

  .search-box {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    max-width: 400px;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);

    &:focus-within {
      border-color: #3182ce;
      box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);

      i {
        color: #3182ce;
      }
    }

    i {
      color: #a0aec0;
      margin-right: 0.75rem;
      font-size: 1rem;
    }

    input {
      flex: 1;
      border: none;
      outline: none;
      font-size: 0.95rem;
      color: #2d3748;
      background: transparent;

      &::placeholder {
        color: #a0aec0;
      }
    }
  }

  .action-buttons {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;

    .btn {
      padding: 0.75rem 1.25rem;
      font-size: 0.9rem;
      border-radius: 10px;
      transition: all 0.3s ease;
      font-weight: 500;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);

      &.btn-outline {
        background: #ffffff;
        border: 1px solid #3182ce;
        color: #3182ce;

        &:hover {
          background: #3182ce;
          color: white;
          transform: translateY(-1px);
          box-shadow: 0 4px 12px rgba(49, 130, 206, 0.3);
        }
      }
    }
  }

  .unsaved-warning {
    display: flex;
    align-items: center;
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
    border: 1px solid #fc8181;
    border-radius: 12px;
    color: #c53030;
    font-size: 0.9rem;
    font-weight: 500;
    animation: slideDown 0.3s ease;
    box-shadow: 0 2px 8px rgba(197, 48, 48, 0.1);

    i {
      margin-right: 0.75rem;
      font-size: 1.1rem;
    }
  }
}

.settings-nav {
  display: flex;
  background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
  border-radius: 12px 12px 0 0;
  padding: 0.5rem;
  gap: 0.25rem;

  .nav-tab {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.5rem;
    background: transparent;
    border: none;
    color: #4a5568;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    border-radius: 10px;
    font-size: 0.9rem;
    position: relative;

    &:hover {
      color: #3182ce;
      background: rgba(49, 130, 206, 0.1);
      transform: translateY(-2px);
    }

    &.active {
      color: #3182ce;
      background: #ffffff;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      font-weight: 600;
    }

    i {
      font-size: 1.1rem;
    }

    .search-indicator {
      color: #3182ce;
      font-weight: bold;
      margin-left: 0.5rem;
      animation: pulse 2s infinite;
    }

    @media (max-width: 768px) {
      padding: 1rem;

      span:not(.search-indicator) {
        display: none;
      }
    }
  }
}

.settings-content {
  padding: 2rem;
  background: #ffffff;
}

.settings-panel {
  .panel-header {
    margin-bottom: 2.5rem;
    text-align: center;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #e2e8f0;

    h3 {
      font-size: 1.75rem;
      font-weight: 700;
      color: #1a365d;
      margin: 0 0 0.75rem 0;
    }

    p {
      color: #4a5568;
      margin: 0;
      font-size: 1.1rem;
      line-height: 1.6;
    }
  }

  animation: fadeInUp 0.6s ease;
}

.settings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 2rem;

  .setting-card {
    background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);

    &:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      border-color: #3182ce;
    }

    .card-header {
      background: linear-gradient(135deg, #3182ce 0%, #2c5aa0 100%);
      padding: 1.5rem;
      display: flex;
      align-items: center;
      gap: 1rem;

      .card-icon {
        width: 52px;
        height: 52px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.4rem;
        backdrop-filter: blur(10px);
      }

      h4 {
        font-size: 1.3rem;
        font-weight: 700;
        color: white;
        margin: 0;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
      }
    }

    .card-content {
      padding: 2rem;

      .form-group {
        margin-bottom: 1.75rem;

        &:last-child {
          margin-bottom: 0;
        }

        label {
          display: block;
          font-weight: 600;
          color: #2d3748;
          margin-bottom: 0.5rem;
          font-size: 0.95rem;
        }

        input,
        textarea,
        select {
          width: 100%;
          padding: 0.875rem 1rem;
          border: 1px solid #e2e8f0;
          border-radius: 10px;
          font-size: 0.95rem;
          transition: all 0.3s ease;
          background: #ffffff;
          color: #2d3748;
          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);

          &:focus {
            outline: none;
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
            transform: translateY(-1px);
          }

          &::placeholder {
            color: #a0aec0;
          }

          &.error {
            border-color: #e53e3e;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
          }
        }

        textarea {
          resize: vertical;
          min-height: 100px;
          font-family: inherit;
        }

        select {
          cursor: pointer;
          background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%234a5568' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
          background-position: right 1rem center;
          background-repeat: no-repeat;
          background-size: 1rem;
          appearance: none;
        }

        .error-text {
          color: #e53e3e;
          font-size: 0.85rem;
          margin-top: 0.5rem;
          display: block;
          font-weight: 500;
        }

        .char-count {
          color: #718096;
          font-size: 0.8rem;
          margin-top: 0.5rem;
          display: block;
          text-align: right;
        }

        small {
          color: #718096;
          font-size: 0.85rem;
          margin-top: 0.5rem;
          display: block;
        }
      }
    }
  }
}

// Color Input Group
.color-input-group {
  display: flex;
  gap: 0.75rem;
  align-items: center;

  .color-picker {
    width: 60px;
    height: 44px;
    padding: 0;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: none;

    &::-webkit-color-swatch-wrapper {
      padding: 0;
      border-radius: 6px;
      overflow: hidden;
    }

    &::-webkit-color-swatch {
      border: none;
      border-radius: 6px;
    }

    &:focus {
      outline: none;
      border-color: #3182ce;
      box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
    }
  }

  .color-text {
    flex: 1;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.9rem;
    text-transform: uppercase;
  }
}

// File Upload Area
.file-upload-area {
  .logo-preview {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-bottom: 1rem;

    .logo-image {
      width: 80px;
      height: 80px;
      object-fit: contain;
      border-radius: 8px;
      background: white;
      padding: 0.5rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-danger {
      background: #e53e3e;
      color: white;
      border: none;
      padding: 0.5rem;
      border-radius: 6px;
      transition: all 0.3s ease;

      &:hover {
        background: #c53030;
        transform: scale(1.05);
      }
    }
  }

  .btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
    width: 100%;
  }
}

// Password Input
.password-input {
  position: relative;
  display: flex;
  align-items: center;

  input {
    padding-right: 3rem;
  }

  .password-toggle {
    position: absolute;
    right: 1rem;
    background: none;
    border: none;
    color: #a0aec0;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: all 0.3s ease;

    &:hover {
      color: #3182ce;
      background: rgba(49, 130, 206, 0.1);
    }

    &:focus {
      outline: none;
      box-shadow: 0 0 0 2px rgba(49, 130, 206, 0.2);
    }
  }
}

// API Key Item
.api-key-item {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1.5rem;
  background: #f7fafc;
  border: 1px solid #e2e8f0;
  border-radius: 12px;

  .api-key-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;

    .key-name {
      font-weight: 600;
      color: #2d3748;
      font-size: 0.9rem;
    }

    .api-key {
      background: #2d3748;
      color: #a0aec0;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
      font-size: 0.85rem;
      word-break: break-all;
      border: 1px solid #4a5568;
    }
  }

  .btn {
    align-self: flex-start;
  }
}

// SMTP Config Animation
.smtp-config {
  animation: slideDown 0.3s ease;
  margin-top: 1rem;
}

// Settings Actions
.settings-actions {
  display: flex;
  gap: 1rem;
  padding: 2rem;
  background: #f7fafc;
  border-top: 1px solid #e2e8f0;
  border-radius: 0 0 16px 16px;
  justify-content: flex-end;
  flex-wrap: wrap;

  .btn {
    padding: 0.875rem 1.5rem;
    font-weight: 600;
    border-radius: 10px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    min-width: 140px;
    justify-content: center;

    &.btn-primary {
      background: linear-gradient(135deg, #3182ce 0%, #2c5aa0 100%);
      color: white;
      border: none;
      box-shadow: 0 4px 12px rgba(49, 130, 206, 0.3);

      &:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(49, 130, 206, 0.4);
      }

      &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: 0 2px 4px rgba(49, 130, 206, 0.2);
      }
    }

    &.btn-warning {
      background: linear-gradient(135deg, #f56500 0%, #dd6b20 100%);
      color: white;
      border: none;
      box-shadow: 0 4px 12px rgba(245, 101, 0, 0.3);

      &:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 101, 0, 0.4);
      }
    }

    &.btn-secondary {
      background: #ffffff;
      color: #4a5568;
      border: 1px solid #e2e8f0;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);

      &:hover {
        background: #f7fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      }
    }

    i.fa-spinner {
      animation: spin 1s linear infinite;
    }
  }

  @media (max-width: 768px) {
    flex-direction: column;

    .btn {
      width: 100%;
      min-width: auto;
    }
  }
}

// Toast Notifications
.toast-container {
  position: fixed;
  top: 2rem;
  right: 2rem;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-width: 400px;
}

.toast-notification {
  display: flex;
  align-items: center;
  padding: 1rem 1.25rem;
  background: #ffffff;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  border-left: 4px solid;
  animation: slideInRight 0.3s ease;
  backdrop-filter: blur(10px);

  &.success {
    border-left-color: #38a169;
    background: linear-gradient(135deg, #f0fff4 0%, #c6f6d5 100%);

    .toast-content i {
      color: #38a169;
    }
  }

  &.error {
    border-left-color: #e53e3e;
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);

    .toast-content i {
      color: #e53e3e;
    }
  }

  &.warning {
    border-left-color: #f56500;
    background: linear-gradient(135deg, #fffaf0 0%, #feebc8 100%);

    .toast-content i {
      color: #f56500;
    }
  }

  &.info {
    border-left-color: #3182ce;
    background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);

    .toast-content i {
      color: #3182ce;
    }
  }

  .toast-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    font-weight: 500;
    font-size: 0.9rem;
    color: #2d3748;

    i {
      font-size: 1.1rem;
    }
  }

  .toast-close {
    background: none;
    border: none;
    color: #718096;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.3s ease;
    margin-left: 1rem;

    &:hover {
      color: #2d3748;
      background: rgba(0, 0, 0, 0.1);
    }
  }
}

.checkbox-group {
  .modern-checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    margin-bottom: 0.75rem;

    &:hover {
      background: rgba(49, 130, 206, 0.08);
      border-color: #3182ce;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(49, 130, 206, 0.15);
    }

    input[type="checkbox"] {
      position: absolute;
      opacity: 0;
      cursor: pointer;
      height: 0;
      width: 0;

      &:checked ~ .checkmark {
        background: linear-gradient(135deg, #3182ce 0%, #2c5aa0 100%);
        border-color: #3182ce;
        transform: scale(1.05);

        &::after {
          display: block;
          transform: rotate(45deg) scale(1);
        }
      }
    }

    .checkmark {
      position: relative;
      height: 28px;
      width: 28px;
      background: #ffffff;
      border: 3px solid #e2e8f0;
      border-radius: 8px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      flex-shrink: 0;

      &::after {
        content: "";
        position: absolute;
        display: none;
        left: 8px;
        top: 4px;
        width: 6px;
        height: 12px;
        border: solid white;
        border-width: 0 3px 3px 0;
        transform: rotate(45deg) scale(0);
        transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      }
    }

    .label-text {
      color: #2d3748;
      font-weight: 500;
      font-size: 1rem;
      line-height: 1.4;
      transition: all 0.3s ease;
    }
  }
}

.content-card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  border: 1px solid #e2e8f0;
}

// Button enhancements
.btn-sm {
  padding: 0.5rem 0.75rem;
  font-size: 0.85rem;
  min-width: auto;
}

// Toast animations
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter {
  opacity: 0;
  transform: translateX(100px);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100px);
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(100px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 768px) {
  .admin-settings {
    padding: 1rem;
  }

  .settings-grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
}
</style>
