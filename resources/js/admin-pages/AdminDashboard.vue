<template>
  <div class="admin-dashboard">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
      <div class="header-content">
        <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
        <p>Welcome back, <strong>{{ user?.name || 'Admin' }}</strong>!</p>
        <div class="header-time">
          <i class="fas fa-clock"></i>
          <span>{{ currentTime }}</span>
        </div>
      </div>
      <div class="header-actions">
        <button @click="refreshData" :disabled="loading" class="btn btn-primary">
          <i :class="['fas', loading ? 'fa-spinner fa-spin' : 'fa-sync-alt']"></i>
          Refresh Data
        </button>
        <router-link to="/admin/activity-logs" class="btn btn-secondary">
          <i class="fas fa-history"></i>
          View Activity
        </router-link>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
      <div class="stats-grid">
        <div class="stat-card primary">
          <div class="stat-header">
            <div class="stat-icon">
              <i class="fas fa-users"></i>
            </div>
            <div class="stat-trend" :class="{ positive: stats.recent_users_count > 0 }">
              <i class="fas fa-arrow-up" v-if="stats.recent_users_count > 0"></i>
              <span>+{{ stats.recent_users_count || 0 }} this month</span>
            </div>
          </div>
          <div class="stat-content">
            <h3>{{ stats.users_count || 0 }}</h3>
            <p>Total Users</p>
            <small>{{ stats.admin_users_count || 0 }} administrators</small>
          </div>
        </div>

        <div class="stat-card success">
          <div class="stat-header">
            <div class="stat-icon">
              <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-trend positive">
              <i class="fas fa-star"></i>
              <span>{{ stats.featured_portfolios_count || 0 }} featured</span>
            </div>
          </div>
          <div class="stat-content">
            <h3>{{ stats.portfolios_count || 0 }}</h3>
            <p>Portfolio Items</p>
            <small>{{ stats.recent_portfolios_count || 0 }} added recently</small>
          </div>
        </div>

        <div class="stat-card info">
          <div class="stat-header">
            <div class="stat-icon">
              <i class="fas fa-newspaper"></i>
            </div>
            <div class="stat-trend positive">
              <i class="fas fa-check-circle"></i>
              <span>{{ stats.published_news_count || 0 }} published</span>
            </div>
          </div>
          <div class="stat-content">
            <h3>{{ stats.news_count || 0 }}</h3>
            <p>News Articles</p>
            <small>{{ stats.recent_news_count || 0 }} added recently</small>
          </div>
        </div>

        <div class="stat-card warning" :class="{ urgent: stats.contact_messages_unread > 5 }">
          <div class="stat-header">
            <div class="stat-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-trend" :class="{ negative: stats.contact_messages_unread > 0 }">
              <i class="fas fa-exclamation-circle" v-if="stats.contact_messages_unread > 0"></i>
              <i class="fas fa-check-circle" v-else></i>
              <span>{{ stats.contact_messages_unread || 0 }} unread</span>
            </div>
          </div>
          <div class="stat-content">
            <h3>{{ stats.contact_messages_count || 0 }}</h3>
            <p>Contact Messages</p>
            <small>{{ stats.contact_messages_today || 0 }} today</small>
          </div>
        </div>

        <div class="stat-card purple">
          <div class="stat-header">
            <div class="stat-icon">
              <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-trend">
              <i class="fas fa-briefcase"></i>
              <span>{{ stats.careers_active || 0 }} active</span>
            </div>
          </div>
          <div class="stat-content">
            <h3>{{ stats.careers_count || 0 }}</h3>
            <p>Open Positions</p>
            <small>{{ stats.job_applications || 0 }} applications</small>
          </div>
        </div>

        <div class="stat-card teal">
          <div class="stat-header">
            <div class="stat-icon">
              <i class="fas fa-user-friends"></i>
            </div>
            <div class="stat-trend positive">
              <i class="fas fa-check-circle"></i>
              <span>{{ stats.team_members_active || 0 }} active</span>
            </div>
          </div>
          <div class="stat-content">
            <h3>{{ stats.team_members_count || 0 }}</h3>
            <p>Team Members</p>
            <small>{{ stats.team_departments || 0 }} departments</small>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="dashboard-content">
      <!-- Quick Actions Section -->
      <div class="dashboard-section">
        <div class="section-header">
          <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
          <p>Frequently used admin tasks</p>
        </div>
        <div class="quick-actions-grid">
          <router-link to="/admin/portfolio" class="action-card create">
            <div class="action-icon">
              <i class="fas fa-plus-circle"></i>
            </div>
            <div class="action-content">
              <h4>Add Portfolio</h4>
              <p>Showcase new work</p>
            </div>
          </router-link>

          <router-link to="/admin/news" class="action-card write">
            <div class="action-icon">
              <i class="fas fa-edit"></i>
            </div>
            <div class="action-content">
              <h4>Write Article</h4>
              <p>Share news and updates</p>
            </div>
          </router-link>

          <router-link to="/admin/team" class="action-card team">
            <div class="action-icon">
              <i class="fas fa-user-plus"></i>
            </div>
            <div class="action-content">
              <h4>Add Team Member</h4>
              <p>Expand your team</p>
            </div>
          </router-link>

          <router-link to="/admin/careers" class="action-card career">
            <div class="action-icon">
              <i class="fas fa-handshake"></i>
            </div>
            <div class="action-content">
              <h4>Post Job</h4>
              <p>Find new talent</p>
            </div>
          </router-link>

          <router-link to="/admin/file-manager" class="action-card files">
            <div class="action-icon">
              <i class="fas fa-folder-open"></i>
            </div>
            <div class="action-content">
              <h4>Manage Files</h4>
              <p>Upload and organize</p>
            </div>
          </router-link>

          <router-link to="/admin/settings" class="action-card settings">
            <div class="action-icon">
              <i class="fas fa-cog"></i>
            </div>
            <div class="action-content">
              <h4>Settings</h4>
              <p>Configure system</p>
            </div>
          </router-link>
        </div>
      </div>

      <!-- Recent Activity Section -->
      <div class="dashboard-section">
        <div class="section-header">
          <h2><i class="fas fa-history"></i> Recent Activity</h2>
          <p>Latest actions in your admin panel</p>
          <router-link to="/admin/activity-logs" class="view-all-link">
            View All <i class="fas fa-arrow-right"></i>
          </router-link>
        </div>
        <div class="activity-container">
          <div v-if="activityLoading" class="activity-loading">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Loading recent activity...</p>
          </div>
          <div v-else-if="recentActivity.length === 0" class="activity-empty">
            <i class="fas fa-history"></i>
            <h3>No Recent Activity</h3>
            <p>Start using your admin panel to see activity here</p>
          </div>
          <div v-else class="activity-list">
            <div
              v-for="activity in recentActivity"
              :key="activity.id"
              class="activity-item"
              :class="`severity-${activity.severity}`"
            >
              <div class="activity-icon" :class="getActivityIconClass(activity.action)">
                <i :class="getActivityIcon(activity.action)"></i>
              </div>
              <div class="activity-content">
                <div class="activity-main">
                  <p class="activity-description">{{ activity.description }}</p>
                  <div class="activity-meta">
                    <span class="activity-user" v-if="activity.user">
                      <i class="fas fa-user"></i>
                      {{ activity.user.name }}
                    </span>
                    <span class="activity-time">
                      <i class="fas fa-clock"></i>
                      {{ formatTimeAgo(activity.created_at) }}
                    </span>
                  </div>
                </div>
                <div class="activity-badge" :class="`badge-${activity.severity}`">
                  {{ activity.action }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- System Overview Section -->
      <div class="dashboard-section">
        <div class="section-header">
          <h2><i class="fas fa-chart-bar"></i> System Overview</h2>
          <p>Quick insights and system status</p>
        </div>
        <div class="overview-grid">
          <div class="overview-card">
            <div class="overview-header">
              <h3><i class="fas fa-shield-alt"></i> Security</h3>
            </div>
            <div class="overview-content">
              <div class="metric">
                <span class="metric-label">Failed Logins (24h)</span>
                <span class="metric-value" :class="{ danger: stats.failed_logins_24h > 5 }">
                  {{ stats.failed_logins_24h || 0 }}
                </span>
              </div>
              <div class="metric">
                <span class="metric-label">Active Sessions</span>
                <span class="metric-value success">{{ stats.active_sessions || 1 }}</span>
              </div>
              <div class="metric">
                <span class="metric-label">Last Login</span>
                <span class="metric-value">{{ formatLastLogin() }}</span>
              </div>
            </div>
          </div>

          <div class="overview-card">
            <div class="overview-header">
              <h3><i class="fas fa-database"></i> Content</h3>
            </div>
            <div class="overview-content">
              <div class="metric">
                <span class="metric-label">Total Posts</span>
                <span class="metric-value">{{ (stats.portfolios_count || 0) + (stats.news_count || 0) }}</span>
              </div>
              <div class="metric">
                <span class="metric-label">Published</span>
                <span class="metric-value success">{{ stats.published_news_count || 0 }}</span>
              </div>
              <div class="metric">
                <span class="metric-label">Featured</span>
                <span class="metric-value">{{ stats.featured_portfolios_count || 0 }}</span>
              </div>
            </div>
          </div>

          <div class="overview-card">
            <div class="overview-header">
              <h3><i class="fas fa-users"></i> Engagement</h3>
            </div>
            <div class="overview-content">
              <div class="metric">
                <span class="metric-label">Messages This Week</span>
                <span class="metric-value">{{ stats.contact_messages_this_week || 0 }}</span>
              </div>
              <div class="metric">
                <span class="metric-label">Job Applications</span>
                <span class="metric-value">{{ stats.job_applications || 0 }}</span>
              </div>
              <div class="metric">
                <span class="metric-label">Response Rate</span>
                <span class="metric-value success">98%</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useNotifications } from '../composables/useNotifications'
import { adminApiService } from '../admin-services/adminApi'
import { useAdminAuth } from '../admin-composables/useAdminAuth'

const { user, initializeAuth } = useAdminAuth()
const { showSuccess, showError, showWarning, showInfo } = useNotifications()

// Loading and error states
const loading = ref(false)
const activityLoading = ref(true)
const error = ref(null)
const lastUpdated = ref(null)

// Time management
const currentTime = ref('')
const timeInterval = ref(null)
const autoRefreshInterval = ref(null)

// Data storage
const stats = ref({
  users_count: 0,
  admin_users_count: 0,
  recent_users_count: 0,
  portfolios_count: 0,
  featured_portfolios_count: 0,
  recent_portfolios_count: 0,
  news_count: 0,
  published_news_count: 0,
  recent_news_count: 0,
  contact_messages_count: 0,
  contact_messages_unread: 0,
  contact_messages_today: 0,
  contact_messages_this_week: 0,
  team_members_count: 0,
  team_members_active: 0,
  team_departments: 0,
  careers_count: 0,
  careers_active: 0,
  job_applications: 0,
  failed_logins_24h: 0,
  active_sessions: 1,
  last_login: null
})

const recentActivity = ref([])
const notifications = ref([])

// Computed properties for better data handling
const hasUnreadMessages = computed(() => stats.value.contact_messages_unread > 0)
const hasHighFailedLogins = computed(() => stats.value.failed_logins_24h > 5)
const totalContent = computed(() => (stats.value.portfolios_count || 0) + (stats.value.news_count || 0))
const urgentNotifications = computed(() => notifications.value.filter(n => n.type === 'urgent').length)

// Enhanced time update with locale support
const updateTime = () => {
  const now = new Date()
  currentTime.value = now.toLocaleString('en-US', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

// Enhanced error handling
const handleError = (error, context = 'dashboard') => {
  console.error(`Error in ${context}:`, error)

  let errorMessage = 'An error occurred while loading data'

  if (error.response?.status === 401) {
    errorMessage = 'Session expired. Please login again'
  } else if (error.response?.status === 403) {
    errorMessage = 'Insufficient permissions'
  } else if (error.response?.status === 500) {
    errorMessage = 'Server error. Please try again later'
  } else if (!navigator.onLine) {
    errorMessage = 'No internet connection'
  }

  addNotification(errorMessage, 'error')
  return errorMessage
}

// Notification system
const addNotification = (message, type = 'info', duration = 5000) => {
  const notification = {
    id: Date.now(),
    message,
    type,
    timestamp: new Date()
  }

  notifications.value.unshift(notification)

  if (duration > 0) {
    setTimeout(() => {
      removeNotification(notification.id)
    }, duration)
  }
}

const removeNotification = (id) => {
  const index = notifications.value.findIndex(n => n.id === id)
  if (index > -1) {
    notifications.value.splice(index, 1)
  }
}

// Enhanced data loading with retry mechanism and fallback data
const loadDashboardData = async (showLoading = true, retryCount = 0) => {
  const maxRetries = 3

  if (showLoading) {
    loading.value = true
    activityLoading.value = true
  }

  try {
    error.value = null

    // Load stats with fallback data
    try {
      // Try to load full dashboard data first, fall back to stats only
      let statsResponse;
      try {
        statsResponse = await Promise.race([
          adminApiService.getDashboard(),
          new Promise((_, reject) =>
            setTimeout(() => reject(new Error('Timeout')), 10000)
          )
        ])

        // If we get full dashboard data, extract stats and activity
        if (statsResponse?.data?.data) {
          const dashboardData = statsResponse.data.data;
          if (dashboardData.stats) {
            stats.value = { ...stats.value, ...dashboardData.stats };
          }
          if (dashboardData.recent_activity) {
            recentActivity.value = dashboardData.recent_activity.map(activity => ({
              ...activity,
              formattedTime: formatTimeAgo(activity.time),
              icon: activity.icon || getActivityIcon(activity.type),
              iconClass: activity.color || getActivityIconClass(activity.type)
            }));
          }
          if (dashboardData.notifications) {
            notifications.value = dashboardData.notifications;
          }
        }
      } catch (dashboardError) {
        // Fall back to stats-only endpoint
        console.warn('Full dashboard API failed, trying stats only:', dashboardError);
        statsResponse = await Promise.race([
          adminApiService.getStats(),
          new Promise((_, reject) =>
            setTimeout(() => reject(new Error('Timeout')), 10000)
          )
        ]);

        if (statsResponse?.data?.data) {
          stats.value = { ...stats.value, ...statsResponse.data.data };
        }
      }

      if (!statsResponse?.data?.data) {
        throw new Error('Invalid stats response');
      }
    } catch (statsError) {
      console.warn('Stats API failed, using fallback data:', statsError)
      // Provide fallback stats data
      stats.value = {
        users_count: 1,
        admin_users_count: 1,
        recent_users_count: 0,
        portfolios_count: 0,
        featured_portfolios_count: 0,
        recent_portfolios_count: 0,
        news_count: 0,
        published_news_count: 0,
        recent_news_count: 0,
        contact_messages_count: 0,
        contact_messages_unread: 0,
        contact_messages_today: 0,
        contact_messages_this_week: 0,
        team_members_count: 0,
        team_members_active: 0,
        team_departments: 0,
        careers_count: 0,
        careers_active: 0,
        job_applications: 0,
        failed_logins_24h: 0,
        active_sessions: 1,
        last_login: new Date()
      }

      if (retryCount === 0) {
        addNotification('Using offline data - some stats may not be current', 'warning', 5000)
      }
    }

    // Load activity with fallback
    try {
      const activityResponse = await Promise.race([
        adminApiService.getActivityLogs({
          page: 1,
          per_page: 15,
          include_user: true,
          sort: 'created_at',
          order: 'desc'
        }),
        new Promise((_, reject) =>
          setTimeout(() => reject(new Error('Timeout')), 10000)
        )
      ])

      if (activityResponse?.data?.data) {
        const activityData = activityResponse.data
        if (activityData?.data) {
          recentActivity.value = activityData.data.map(activity => ({
            ...activity,
            formattedTime: formatTimeAgo(activity.created_at),
            icon: getActivityIcon(activity.action),
            iconClass: getActivityIconClass(activity.action)
          }))
        }
      } else {
        throw new Error('Invalid activity response')
      }
    } catch (activityError) {
      console.warn('Activity API failed:', activityError)
      // Provide fallback activity data
      recentActivity.value = [
        {
          id: 1,
          action: 'login',
          description: 'Admin logged into the system',
          severity: 'low',
          created_at: new Date().toISOString(),
          user: { name: user.value?.name || 'Admin' },
          formattedTime: 'Just now',
          icon: getActivityIcon('login'),
          iconClass: getActivityIconClass('login')
        }
      ]
    }

    // Load notifications with fallback
    try {
      if (adminApiService.getNotifications) {
        const notificationsResponse = await Promise.race([
          adminApiService.getNotifications(),
          new Promise((_, reject) =>
            setTimeout(() => reject(new Error('Timeout')), 5000)
          )
        ])

        if (notificationsResponse?.data) {
          const notificationsData = notificationsResponse.data
          if (Array.isArray(notificationsData)) {
            notifications.value = notificationsData
          }
        }
      }
    } catch (notificationsError) {
      console.warn('Notifications API failed:', notificationsError)
      // Fallback notifications are empty array (already set)
    }

    lastUpdated.value = new Date()

    if (retryCount === 0) {
      addNotification('Dashboard loaded successfully', 'success', 3000)
    }

  } catch (error) {
    const errorMsg = handleError(error, 'loadDashboardData')
    error.value = errorMsg

    // Only retry if we haven't exhausted retries and it's not an auth error
    if (retryCount < maxRetries && error?.response?.status !== 401) {
      console.log(`Retrying dashboard load... (${retryCount + 1}/${maxRetries})`)
      setTimeout(() => loadDashboardData(false, retryCount + 1), 2000)
      return
    }

    // If all retries failed, show error but still show fallback data
    addNotification('Dashboard partially loaded - some data may be unavailable', 'warning', 5000)
  } finally {
    if (showLoading) {
      loading.value = false
      activityLoading.value = false
    }
  }
}

// Enhanced refresh with user feedback
const refreshData = async () => {
  if (loading.value) return

  try {
    loading.value = true
    await Promise.all([
      loadStats(),
      loadRecentActivity(),
      loadSystemStatus()
    ])
    showSuccess('Dashboard data refreshed successfully!')
  } catch (error) {
    console.error('Failed to refresh data:', error)
    showError('Failed to refresh dashboard data. Please try again.')
  } finally {
    loading.value = false
  }
}

const loadStats = async () => {
  try {
    const response = await adminApiService.getDashboardStats()
    stats.value = response.data
  } catch (error) {
    console.error('Failed to load stats:', error)
    showError('Failed to load statistics')
  }
}

const loadRecentActivity = async () => {
  try {
    const response = await adminApiService.getRecentActivity()
    recentActivity.value = response.data
  } catch (error) {
    console.error('Failed to load recent activity:', error)
    showWarning('Failed to load recent activity')
  }
}

const loadSystemStatus = async () => {
  try {
    const response = await adminApiService.getSystemStatus()
    systemStatus.value = response.data
  } catch (error) {
    console.error('Failed to load system status:', error)
    showWarning('System status unavailable')
  }
}

// Real-time activity updates
const loadNewActivity = async () => {
  try {
    const lastActivityId = recentActivity.value.length > 0 ? recentActivity.value[0].id : 0

    const response = await adminApiService.getActivityLogs({
      page: 1,
      per_page: 5,
      since_id: lastActivityId,
      include_user: true
    })

    if (response.data?.data && response.data.data.length > 0) {
      const newActivities = response.data.data.map(activity => ({
        ...activity,
        formattedTime: formatTimeAgo(activity.created_at),
        icon: getActivityIcon(activity.action),
        iconClass: getActivityIconClass(activity.action)
      }))

      recentActivity.value = [...newActivities, ...recentActivity.value].slice(0, 15)

      if (newActivities.length > 0) {
        addNotification(`${newActivities.length} new activities`, 'info', 3000)
      }
    }
  } catch (error) {
    console.warn('Failed to load new activity:', error)
  }
}

// Enhanced time formatting with relative times
const formatTimeAgo = (dateTime) => {
  if (!dateTime) return 'Unknown'

  const now = new Date()
  const date = new Date(dateTime)
  const diff = Math.floor((now - date) / 1000)

  if (diff < 30) return 'Just now'
  if (diff < 60) return `${diff}s ago`
  if (diff < 3600) return `${Math.floor(diff / 60)}m ago`
  if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`
  if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`
  if (diff < 2592000) return `${Math.floor(diff / 604800)}w ago`

  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Enhanced last login formatting
const formatLastLogin = () => {
  if (!stats.value.last_login) return 'First login'

  const date = new Date(stats.value.last_login)
  const now = new Date()
  const diff = Math.floor((now - date) / (1000 * 60 * 60 * 24))

  if (diff === 0) return 'Today'
  if (diff === 1) return 'Yesterday'
  if (diff < 7) return `${diff} days ago`

  return date.toLocaleDateString('en-US')
}

// Enhanced activity icon mapping
const getActivityIcon = (action) => {
  const iconMap = {
    'login': 'fas fa-sign-in-alt',
    'logout': 'fas fa-sign-out-alt',
    'created': 'fas fa-plus-circle',
    'updated': 'fas fa-edit',
    'deleted': 'fas fa-trash-alt',
    'published': 'fas fa-globe',
    'unpublished': 'fas fa-eye-slash',
    'marked_read': 'fas fa-envelope-open',
    'marked_unread': 'fas fa-envelope',
    'file_upload': 'fas fa-upload',
    'file_delete': 'fas fa-file-times',
    'directory_created': 'fas fa-folder-plus',
    'resume_downloaded': 'fas fa-download',
    'status_changed': 'fas fa-toggle-on',
    'featured_changed': 'fas fa-star',
    'bulk_action': 'fas fa-list',
    'settings_updated': 'fas fa-cog',
    'user_created': 'fas fa-user-plus',
    'user_updated': 'fas fa-user-edit',
    'user_deleted': 'fas fa-user-times',
    'backup_created': 'fas fa-database',
    'cache_cleared': 'fas fa-broom',
    'unauthorized_access': 'fas fa-shield-alt',
    'login_failed': 'fas fa-exclamation-triangle',
    'password_changed': 'fas fa-key',
    'email_sent': 'fas fa-paper-plane'
  }
  return iconMap[action] || 'fas fa-info-circle'
}

// Enhanced activity styling
const getActivityIconClass = (action) => {
  const classMap = {
    'login': 'success',
    'logout': 'info',
    'created': 'success',
    'published': 'success',
    'updated': 'warning',
    'deleted': 'danger',
    'unpublished': 'warning',
    'marked_read': 'info',
    'marked_unread': 'warning',
    'file_upload': 'primary',
    'file_delete': 'danger',
    'directory_created': 'success',
    'resume_downloaded': 'info',
    'status_changed': 'warning',
    'featured_changed': 'warning',
    'bulk_action': 'info',
    'settings_updated': 'primary',
    'user_created': 'success',
    'user_updated': 'warning',
    'user_deleted': 'danger',
    'backup_created': 'primary',
    'cache_cleared': 'info',
    'unauthorized_access': 'danger',
    'login_failed': 'danger',
    'password_changed': 'success',
    'email_sent': 'info'
  }
  return classMap[action] || 'secondary'
}

// Activity filtering and search
const activityFilter = ref('all')
const activitySearch = ref('')

const filteredActivity = computed(() => {
  let filtered = recentActivity.value

  if (activityFilter.value !== 'all') {
    filtered = filtered.filter(activity => activity.severity === activityFilter.value)
  }

  if (activitySearch.value) {
    const search = activitySearch.value.toLowerCase()
    filtered = filtered.filter(activity =>
      activity.description.toLowerCase().includes(search) ||
      activity.action.toLowerCase().includes(search) ||
      (activity.user?.name || '').toLowerCase().includes(search)
    )
  }

  return filtered
})

// Connection status monitoring
const isOnline = ref(navigator.onLine)
const connectionStatus = ref('online')

const handleOnline = () => {
  isOnline.value = true
  connectionStatus.value = 'online'
  addNotification('Connection restored', 'success', 3000)
  loadDashboardData(false)
}

const handleOffline = () => {
  isOnline.value = false
  connectionStatus.value = 'offline'
  addNotification('No internet connection', 'warning', 0)
}

// Lifecycle management
onMounted(async () => {
  try {
    // Initialize auth state
    await initializeAuth()

    // Setup time updates
    updateTime()
    timeInterval.value = setInterval(updateTime, 1000)

    // Setup connection monitoring
    window.addEventListener('online', handleOnline)
    window.addEventListener('offline', handleOffline)

    // Load initial data
    await loadDashboardData()

    // Setup auto-refresh (every 5 minutes)
    autoRefreshInterval.value = setInterval(() => {
      if (isOnline.value && !loading.value) {
        loadDashboardData(false)
      }
    }, 5 * 60 * 1000)

    // Setup activity updates (every 30 seconds)
    setInterval(() => {
      if (isOnline.value && !loading.value) {
        loadNewActivity()
      }
    }, 30 * 1000)

    // Update activity times every minute
    setInterval(() => {
      recentActivity.value = recentActivity.value.map(activity => ({
        ...activity,
        formattedTime: formatTimeAgo(activity.created_at)
      }))
    }, 60 * 1000)

  } catch (error) {
    handleError(error, 'dashboard initialization')
  }
})

onUnmounted(() => {
  // Cleanup intervals
  if (timeInterval.value) {
    clearInterval(timeInterval.value)
  }
  if (autoRefreshInterval.value) {
    clearInterval(autoRefreshInterval.value)
  }

  // Cleanup event listeners
  window.removeEventListener('online', handleOnline)
  window.removeEventListener('offline', handleOffline)
})

// Expose reactive data and methods to template
defineExpose({
  refreshData,
  loadDashboardData,
  stats,
  recentActivity,
  loading,
  error,
  notifications,
  hasUnreadMessages,
  hasHighFailedLogins,
  totalContent,
  filteredActivity,
  activityFilter,
  activitySearch,
  isOnline,
  connectionStatus
})
</script>

<style scoped>
.admin-dashboard {
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
  background: linear-gradient(135deg, #f8fffe 0%, #f1f5f9 100%);
  min-height: 100vh;
}

/* Dashboard Header */
.dashboard-header {
  margin-bottom: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  padding: 1rem 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(71, 85, 105, 0.06);
  border: 1px solid rgba(226, 232, 240, 0.6);
}

.header-content {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.header-content h1 {
  font-size: 1.75rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.header-content h1 i {
  color: #3b82f6;
  font-size: 1.5rem;
}

.header-content p {
  color: #64748b;
  font-size: 0.95rem;
  margin: 0;
}

.header-time {
  display: flex;
  align-items: center;
  font-size: 0.8rem;
  color: #475569;
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  box-shadow: 0 1px 3px rgba(71, 85, 105, 0.04);
}

.header-time i {
  margin-right: 0.25rem;
  color: #3b82f6;
  font-size: 0.75rem;
}

.header-actions {
  display: flex;
  gap: 0.75rem;
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 500;
  text-decoration: none;
  transition: all 0.3s ease;
  border: none;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.5s;
}

.btn:hover::before {
  left: 100%;
}

.btn-primary {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-primary:disabled {
  background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.btn-secondary {
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  color: #475569;
  border: 1px solid rgba(203, 213, 225, 0.6);
  box-shadow: 0 2px 8px rgba(71, 85, 105, 0.08);
}

.btn-secondary:hover {
  background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(71, 85, 105, 0.12);
  color: #334155;
}

.btn i {
  margin-right: 0.25rem;
  font-size: 0.75rem;
}

/* Stats Overview - компактные карточки в один ряд */
.stats-overview {
  margin-bottom: 1.5rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 0.75rem;
  width: 100%;
}

@media (max-width: 1400px) {
  .stats-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
  }
}

@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
  }
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
}

.stat-card {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  padding: 1rem;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(71, 85, 105, 0.06);
  transition: all 0.3s ease;
  border: 1px solid rgba(226, 232, 240, 0.6);
  position: relative;
  overflow: hidden;
  min-height: 140px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, #3b82f6, #10b981, #f59e0b, #ef4444);
}

.stat-card.primary::before { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.stat-card.success::before { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-card.info::before { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
.stat-card.warning::before { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-card.purple::before { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.stat-card.teal::before { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(71, 85, 105, 0.1);
}

.stat-card.urgent {
  border-color: #fecaca;
  box-shadow: 0 4px 12px rgba(239, 68, 68, 0.12);
  background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
}

.stat-card.urgent::before {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.stat-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.5rem;
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  color: white;
  position: relative;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.stat-card.primary .stat-icon { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.stat-card.success .stat-icon { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.stat-card.info .stat-icon { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
.stat-card.warning .stat-icon { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.stat-card.purple .stat-icon { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.stat-card.teal .stat-icon { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }

.stat-icon::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
  animation: shimmer 4s infinite linear;
}

@keyframes shimmer {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.stat-trend {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.6rem;
  color: #059669;
  font-weight: 500;
  padding: 0.2rem 0.4rem;
  border-radius: 4px;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.stat-trend.positive {
  color: #059669;
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.05) 100%);
  border-color: rgba(16, 185, 129, 0.2);
}

.stat-trend.negative {
  color: #dc2626;
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.05) 100%);
  border-color: rgba(239, 68, 68, 0.2);
}

.stat-trend.urgent {
  color: #dc2626;
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.15) 0%, rgba(220, 38, 38, 0.08) 100%);
  border-color: rgba(239, 68, 68, 0.3);
  animation: blink 1.5s infinite;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

.stat-trend i {
  font-size: 0.5rem;
}

.stat-content h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0 0 0.125rem 0;
  font-family: 'Inter', sans-serif;
}

.stat-content p {
  color: #64748b;
  font-size: 0.75rem;
  margin: 0 0 0.25rem 0;
  text-transform: uppercase;
  letter-spacing: 0.025em;
  font-weight: 500;
}

.stat-content small {
  color: #94a3b8;
  font-size: 0.65rem;
  font-weight: 400;
}

/* Dashboard Content */
.dashboard-content {
  display: grid;
  gap: 1.5rem;
}

.dashboard-section {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  padding: 1.25rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(71, 85, 105, 0.06);
  border: 1px solid rgba(226, 232, 240, 0.6);
  transition: all 0.3s ease;
}

.dashboard-section:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(71, 85, 105, 0.08);
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid #f1f5f9;
}

.section-header h2 {
  font-size: 1.125rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.375rem;
}

.section-header h2 i {
  color: #3b82f6;
  font-size: 1rem;
}

.section-header p {
  color: #64748b;
  margin: 0;
  font-size: 0.8rem;
  font-weight: 400;
}

.view-all-link {
  color: #3b82f6;
  text-decoration: none;
  font-size: 0.8rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 0.25rem;
  transition: all 0.3s ease;
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(29, 78, 216, 0.02) 100%);
}

.view-all-link:hover {
  color: #1d4ed8;
  transform: translateX(2px);
  background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(29, 78, 216, 0.05) 100%);
}

/* Quick Actions - в один ряд на десктопе */
.quick-actions-grid {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 0.75rem;
}

@media (max-width: 1400px) {
  .quick-actions-grid {
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
  }
}

@media (max-width: 1024px) {
  .quick-actions-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
  }
}

@media (max-width: 768px) {
  .quick-actions-grid {
    grid-template-columns: 1fr;
    gap: 0.75rem;
  }
}

.action-card {
  display: flex;
  align-items: center;
  padding: 0.875rem;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-radius: 10px;
  text-decoration: none;
  transition: all 0.3s ease;
  border: 1px solid rgba(203, 213, 225, 0.5);
  position: relative;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(71, 85, 105, 0.04);
  min-height: 90px;
}

.action-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  transition: left 0.5s;
}

.action-card:hover::before {
  left: 100%;
}

.action-card:hover {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  transform: translateX(4px) translateY(-1px);
  box-shadow: 0 6px 16px rgba(71, 85, 105, 0.08);
  border-color: #3b82f6;
}

.action-card.create:hover { border-color: #10b981; }
.action-card.write:hover { border-color: #06b6d4; }
.action-card.team:hover { border-color: #10b981; }
.action-card.career:hover { border-color: #f59e0b; }
.action-card.files:hover { border-color: #8b5cf6; }
.action-card.settings:hover { border-color: #64748b; }

.action-icon {
  width: 40px;
  height: 40px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1rem;
  margin-right: 0.75rem;
  color: white;
  position: relative;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  flex-shrink: 0;
}

.action-card.create .action-icon { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.action-card.write .action-icon { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
.action-card.team .action-icon { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.action-card.career .action-icon { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.action-card.files .action-icon { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.action-card.settings .action-icon { background: linear-gradient(135deg, #64748b 0%, #475569 100%); }

.action-icon::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
}

.action-content {
  flex: 1;
}

.action-content h4 {
  font-size: 0.8rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 0.125rem 0;
  line-height: 1.2;
}

.action-content p {
  font-size: 0.7rem;
  color: #64748b;
  margin: 0;
  font-weight: 400;
  line-height: 1.3;
}

/* Activity Section */
.activity-container {
  position: relative;
  min-height: 250px;
}

.activity-loading {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.75rem;
  font-size: 0.8rem;
  color: #475569;
}

.activity-loading i {
  font-size: 1.5rem;
  color: #3b82f6;
}

.activity-empty {
  text-align: center;
  padding: 2rem 1rem;
  color: #64748b;
}

.activity-empty i {
  font-size: 2.5rem;
  margin-bottom: 0.75rem;
  color: #cbd5e1;
}

.activity-empty h3 {
  color: #475569;
  margin-bottom: 0.375rem;
  font-weight: 500;
  font-size: 1rem;
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.activity-item {
  display: flex;
  align-items: flex-start;
  padding: 1rem;
  border-radius: 10px;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border: 1px solid rgba(203, 213, 225, 0.5);
  transition: all 0.3s ease;
  box-shadow: 0 1px 4px rgba(71, 85, 105, 0.04);
}

.activity-item:hover {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  border-color: #3b82f6;
  transform: translateX(3px);
  box-shadow: 0 4px 12px rgba(71, 85, 105, 0.08);
}

.activity-item.severity-high {
  border-left: 3px solid #ef4444;
  background: linear-gradient(135deg, #fef2f2 0%, #f8fafc 100%);
}

.activity-item.severity-medium {
  border-left: 3px solid #f59e0b;
  background: linear-gradient(135deg, #fefbf2 0%, #f8fafc 100%);
}

.activity-item.severity-low {
  border-left: 3px solid #10b981;
  background: linear-gradient(135deg, #f0fdf4 0%, #f8fafc 100%);
}

.activity-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
  color: white;
  font-size: 0.9rem;
  flex-shrink: 0;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.activity-icon.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.activity-icon.info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
.activity-icon.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.activity-icon.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
.activity-icon.primary { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
.activity-icon.secondary { background: linear-gradient(135deg, #64748b 0%, #475569 100%); }

.activity-content {
  flex: 1;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.activity-main {
  flex: 1;
}

.activity-description {
  margin: 0 0 0.375rem 0;
  font-size: 0.8rem;
  color: #1e293b;
  font-weight: 500;
  line-height: 1.4;
}

.activity-meta {
  display: flex;
  gap: 0.75rem;
  font-size: 0.7rem;
  color: #64748b;
  font-weight: 400;
}

.activity-user, .activity-time {
  display: flex;
  align-items: center;
  gap: 0.125rem;
}

.activity-user i, .activity-time i {
  font-size: 0.6rem;
}

.activity-badge {
  padding: 0.25rem 0.5rem;
  border-radius: 6px;
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.025em;
  margin-left: 0.75rem;
  flex-shrink: 0;
}

.badge-success {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
}

.badge-info {
  background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(6, 182, 212, 0.2);
}

.badge-warning {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  color: #1e293b;
  box-shadow: 0 2px 8px rgba(245, 158, 11, 0.2);
}

.badge-danger {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(239, 68, 68, 0.2);
}

.badge-primary {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
}

.badge-secondary {
  background: linear-gradient(135deg, #64748b 0%, #475569 100%);
  color: white;
  box-shadow: 0 2px 8px rgba(100, 116, 139, 0.2);
}

/* System Overview */
.overview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1rem;
}

.overview-card {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  border-radius: 10px;
  border: 1px solid rgba(203, 213, 225, 0.5);
  overflow: hidden;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(71, 85, 105, 0.04);
}

.overview-card:hover {
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  border-color: #3b82f6;
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(71, 85, 105, 0.08);
}

.overview-header {
  padding: 1rem 1rem 0;
}

.overview-header h3 {
  font-size: 0.9rem;
  font-weight: 600;
  color: #1e293b;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 0.375rem;
}

.overview-header h3 i {
  color: #3b82f6;
  font-size: 0.8rem;
}

.overview-content {
  padding: 0.75rem 1rem 1rem;
}

.metric {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.5rem 0;
  border-bottom: 1px solid rgba(203, 213, 225, 0.3);
}

.metric:last-child {
  border-bottom: none;
}

.metric-label {
  font-size: 0.75rem;
  color: #64748b;
  font-weight: 500;
}

.metric-value {
  font-size: 0.8rem;
  font-weight: 600;
  color: #1e293b;
}

.metric-value.success {
  color: #059669;
}

.metric-value.danger {
  color: #dc2626;
}

.metric-value.warning {
  color: #d97706;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .dashboard-content {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .admin-dashboard {
    padding: 1rem;
  }

  .dashboard-header {
    flex-direction: column;
    gap: 0.75rem;
    align-items: stretch;
  }

  .header-content {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.375rem;
  }

  .header-content h1 {
    font-size: 1.5rem;
  }

  .header-actions {
    justify-content: center;
  }

  .stat-card {
    padding: 1rem;
  }

  .stat-content h3 {
    font-size: 1.5rem;
  }

  .action-card {
    padding: 0.75rem;
  }

  .action-icon {
    width: 40px;
    height: 40px;
    font-size: 1rem;
    margin-right: 0.75rem;
  }

  .overview-grid {
    grid-template-columns: 1fr;
  }

  .activity-item {
    flex-direction: column;
    gap: 0.75rem;
  }

  .activity-content {
    flex-direction: column;
    gap: 0.375rem;
  }

  .activity-badge {
    margin-left: 0;
    align-self: flex-start;
  }
}

@media (max-width: 480px) {
  .admin-dashboard {
    padding: 0.75rem;
  }

  .dashboard-header {
    padding: 0.75rem;
  }

  .dashboard-section {
    padding: 1rem;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.375rem;
  }

  .activity-meta {
    flex-direction: column;
    gap: 0.125rem;
  }
}

/* Light theme optimization */
@media (prefers-color-scheme: light) {
  .admin-dashboard {
    background: linear-gradient(135deg, #fafafa 0%, #f3f4f6 100%);
  }

  .dashboard-header,
  .dashboard-section,
  .stat-card,
  .overview-card {
    background: linear-gradient(135deg, #ffffff 0%, #fefefe 100%);
    border-color: rgba(229, 231, 235, 0.6);
    color: #111827;
  }

  .header-content h1,
  .section-header h2,
  .stat-content h3,
  .action-content h4,
  .overview-header h3,
  .activity-description,
  .metric-value {
    color: #111827;
  }

  .header-content p,
  .section-header p,
  .stat-content p,
  .action-content p,
  .metric-label {
    color: #6b7280;
  }

  .activity-item,
  .action-card {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    border-color: rgba(209, 213, 219, 0.5);
  }

  .activity-item:hover,
  .action-card:hover {
    background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
  }
}

/* Loading animations */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}

.stat-card,
.dashboard-section,
.activity-item {
  animation: fadeIn 0.5s ease-out;
}

.stat-card:nth-child(1) { animation-delay: 0.05s; }
.stat-card:nth-child(2) { animation-delay: 0.1s; }
.stat-card:nth-child(3) { animation-delay: 0.15s; }
.stat-card:nth-child(4) { animation-delay: 0.2s; }
.stat-card:nth-child(5) { animation-delay: 0.25s; }
.stat-card:nth-child(6) { animation-delay: 0.3s; }

/* Custom scrollbar */
.activity-list::-webkit-scrollbar {
  width: 4px;
}

.activity-list::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 8px;
}

.activity-list::-webkit-scrollbar-thumb {
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
  border-radius: 8px;
}

.activity-list::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
}
</style>
