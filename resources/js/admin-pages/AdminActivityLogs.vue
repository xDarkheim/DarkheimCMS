<template>
  <div class="admin-activity-logs">
    <div class="page-header">
      <div class="header-content">
        <h1><i class="fas fa-history"></i> Activity Logs</h1>
        <p>Monitor and audit all system activities</p>
      </div>
      <div class="header-actions">
        <button @click="showExportModal = true" class="btn btn-primary">
          <i class="fas fa-download"></i> Export
        </button>
        <button @click="showCleanupModal = true" class="btn btn-warning">
          <i class="fas fa-broom"></i> Cleanup
        </button>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-grid" v-if="stats">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.total_activities }}</h3>
          <p>Total Activities</p>
          <small>{{ stats.today_activities }} today</small>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon critical">
          <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.security_alerts }}</h3>
          <p>Security Alerts</p>
          <small>Last 30 days</small>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon warning">
          <i class="fas fa-user-times"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.failed_logins_24h }}</h3>
          <p>Failed Logins</p>
          <small>Last 24 hours</small>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-calendar-week"></i>
        </div>
        <div class="stat-content">
          <h3>{{ stats.this_week_activities }}</h3>
          <p>This Week</p>
          <small>{{ stats.this_month_activities }} this month</small>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filter-section">
      <div class="filter-row">
        <div class="filter-group">
          <label>Search:</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search activities..."
            @input="debounceSearch"
          >
        </div>

        <div class="filter-group">
          <label>User:</label>
          <select v-model="filters.user_id">
            <option value="">All Users</option>
            <option v-for="user in filterOptions.users" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.email }})
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label>Action:</label>
          <select v-model="filters.action">
            <option value="">All Actions</option>
            <option v-for="action in filterOptions.actions" :key="action" :value="action">
              {{ action }}
            </option>
          </select>
        </div>

        <div class="filter-group">
          <label>Severity:</label>
          <select v-model="filters.severity">
            <option value="">All Severities</option>
            <option v-for="severity in filterOptions.severities" :key="severity" :value="severity">
              {{ severity }}
            </option>
          </select>
        </div>
      </div>

      <div class="filter-row">
        <div class="filter-group">
          <label>From:</label>
          <input v-model="filters.date_from" type="date">
        </div>

        <div class="filter-group">
          <label>To:</label>
          <input v-model="filters.date_to" type="date">
        </div>

        <div class="filter-group">
          <label>Model:</label>
          <select v-model="filters.model_type">
            <option value="">All Models</option>
            <option v-for="model in filterOptions.model_types" :key="model" :value="model">
              {{ model.split('\\').pop() }}
            </option>
          </select>
        </div>

        <div class="filter-actions">
          <button @click="applyFilters" class="btn btn-primary">
            <i class="fas fa-filter"></i> Apply
          </button>
          <button @click="clearFilters" class="btn btn-secondary">
            <i class="fas fa-times"></i> Clear
          </button>
        </div>
      </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="content-card">
      <div v-if="loading" class="loading-state">
        <i class="fas fa-spinner fa-spin"></i>
        <p>Loading activity logs...</p>
      </div>

      <div v-else-if="!logs.data?.length" class="empty-state">
        <i class="fas fa-history"></i>
        <h3>No Activity Found</h3>
        <p>No activities match your current filters</p>
      </div>

      <div v-else class="table-container">
        <table class="data-table">
          <thead>
            <tr>
              <th>Time</th>
              <th>User</th>
              <th>Action</th>
              <th>Description</th>
              <th>IP Address</th>
              <th>Severity</th>
              <th>Changes</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="log in logs.data" :key="log.id" :class="['log-row', `severity-${log.severity}`]">
              <td class="time-cell">
                <span class="date">{{ formatDate(log.created_at) }}</span>
                <span class="time">{{ formatTime(log.created_at) }}</span>
              </td>
              <td class="user-cell">
                <div v-if="log.user" class="user-info">
                  <strong>{{ log.user.name }}</strong>
                  <small>{{ log.user.email }}</small>
                </div>
                <span v-else class="system-user">System</span>
              </td>
              <td class="action-cell">
                <span :class="['action-badge', `action-${log.action}`]">
                  {{ log.action }}
                </span>
              </td>
              <td class="description-cell">
                <p>{{ log.description }}</p>
                <small v-if="log.model_type">
                  {{ log.model_type.split('\\').pop() }}
                  <span v-if="log.model_id">#{{ log.model_id }}</span>
                </small>
              </td>
              <td class="ip-cell">{{ log.ip_address || '-' }}</td>
              <td class="severity-cell">
                <span :class="['severity-badge', `severity-${log.severity}`]">
                  {{ log.severity }}
                </span>
              </td>
              <td class="changes-cell">
                <button
                  v-if="log.changes"
                  @click="showChanges(log)"
                  class="btn btn-sm btn-outline"
                >
                  <i class="fas fa-eye"></i> View
                </button>
                <span v-else>-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="logs.last_page > 1" class="pagination">
        <button
          @click="changePage(logs.current_page - 1)"
          :disabled="logs.current_page === 1"
          class="btn btn-secondary"
        >
          <i class="fas fa-chevron-left"></i> Previous
        </button>

        <span class="page-info">
          Page {{ logs.current_page }} of {{ logs.last_page }}
          ({{ logs.total }} total)
        </span>

        <button
          @click="changePage(logs.current_page + 1)"
          :disabled="logs.current_page === logs.last_page"
          class="btn btn-secondary"
        >
          Next <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Export Modal -->
    <div v-if="showExportModal" class="modal-overlay" @click.self="showExportModal = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Export Activity Logs</h3>
          <button @click="showExportModal = false" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Export Format</label>
            <select v-model="exportFormat">
              <option value="csv">CSV</option>
              <option value="json">JSON</option>
            </select>
          </div>
          <div class="form-group">
            <label>Date Range</label>
            <div class="date-range">
              <input v-model="exportDateFrom" type="date" required>
              <span>to</span>
              <input v-model="exportDateTo" type="date" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showExportModal = false" class="btn btn-secondary">Cancel</button>
          <button @click="exportLogs" :disabled="exporting" class="btn btn-primary">
            <i v-if="exporting" class="fas fa-spinner fa-spin"></i>
            <i v-else class="fas fa-download"></i>
            {{ exporting ? 'Exporting...' : 'Export' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Cleanup Modal -->
    <div v-if="showCleanupModal" class="modal-overlay" @click.self="showCleanupModal = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Cleanup Old Logs</h3>
          <button @click="showCleanupModal = false" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            This will permanently delete activity logs older than the specified number of days.
            Critical logs will be preserved.
          </div>
          <div class="form-group">
            <label>Keep logs from the last</label>
            <div class="input-group">
              <input v-model="cleanupDays" type="number" min="30" max="365">
              <span>days</span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button @click="showCleanupModal = false" class="btn btn-secondary">Cancel</button>
          <button @click="cleanupLogs" :disabled="cleaning" class="btn btn-warning">
            <i v-if="cleaning" class="fas fa-spinner fa-spin"></i>
            <i v-else class="fas fa-broom"></i>
            {{ cleaning ? 'Cleaning...' : 'Cleanup' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Changes Modal -->
    <div v-if="showChangesModal" class="modal-overlay" @click.self="showChangesModal = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Activity Changes</h3>
          <button @click="showChangesModal = false" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
          <div v-if="selectedLog" class="changes-detail">
            <h4>{{ selectedLog.description }}</h4>
            <div class="changes-list">
              <div v-for="(change, field) in selectedLog.changes" :key="field" class="change-item">
                <strong>{{ field }}:</strong>
                <div class="change-values">
                  <span class="old-value">{{ change.old || 'null' }}</span>
                  <i class="fas fa-arrow-right"></i>
                  <span class="new-value">{{ change.new || 'null' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { adminApiService } from '../admin-services/adminApi.js'

export default {
  name: 'AdminActivityLogs',
  data() {
    return {
      logs: { data: [] },
      stats: null,
      filterOptions: {
        users: [],
        actions: [],
        severities: [],
        model_types: []
      },
      filters: {
        search: '',
        user_id: '',
        action: '',
        severity: '',
        model_type: '',
        date_from: '',
        date_to: '',
        page: 1
      },
      loading: false,
      searchTimeout: null,

      // Export
      showExportModal: false,
      exportFormat: 'csv',
      exportDateFrom: '',
      exportDateTo: '',
      exporting: false,

      // Cleanup
      showCleanupModal: false,
      cleanupDays: 90,
      cleaning: false,

      // Changes modal
      showChangesModal: false,
      selectedLog: null
    }
  },
  async mounted() {
    await this.loadFilterOptions()
    await this.loadStats()
    await this.loadLogs()
  },
  methods: {
    async loadLogs() {
      this.loading = true
      try {
        const response = await adminApiService.getActivityLogs(this.filters)
        this.logs = response.data.data
      } catch (error) {
        console.error('Failed to load activity logs:', error)
        this.$toast?.error('Failed to load activity logs')
      } finally {
        this.loading = false
      }
    },

    async loadStats() {
      try {
        const response = await adminApiService.getActivityLogStats()
        this.stats = response.data.data
      } catch (error) {
        console.error('Failed to load stats:', error)
      }
    },

    async loadFilterOptions() {
      try {
        const response = await adminApiService.getActivityLogFilterOptions()
        this.filterOptions = response.data.data
      } catch (error) {
        console.error('Failed to load filter options:', error)
      }
    },

    applyFilters() {
      this.filters.page = 1
      this.loadLogs()
    },

    clearFilters() {
      this.filters = {
        search: '',
        user_id: '',
        action: '',
        severity: '',
        model_type: '',
        date_from: '',
        date_to: '',
        page: 1
      }
      this.loadLogs()
    },

    debounceSearch() {
      clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => {
        this.applyFilters()
      }, 500)
    },

    changePage(page) {
      this.filters.page = page
      this.loadLogs()
    },

    showChanges(log) {
      this.selectedLog = log
      this.showChangesModal = true
    },

    async exportLogs() {
      if (!this.exportDateFrom || !this.exportDateTo) {
        this.$toast?.error('Please select date range')
        return
      }

      this.exporting = true
      try {
        await adminApiService.exportActivityLogs({
          format: this.exportFormat,
          date_from: this.exportDateFrom,
          date_to: this.exportDateTo
        })
        this.showExportModal = false
        this.$toast?.success('Export completed')
      } catch (error) {
        console.error('Export failed:', error)
        this.$toast?.error('Export failed')
      } finally {
        this.exporting = false
      }
    },

    async cleanupLogs() {
      if (this.cleanupDays < 30) {
        this.$toast?.error('Must keep at least 30 days of logs')
        return
      }

      this.cleaning = true
      try {
        const response = await adminApiService.cleanupActivityLogs(this.cleanupDays)
        this.showCleanupModal = false
        this.$toast?.success(response.data.message)
        await this.loadStats()
        await this.loadLogs()
      } catch (error) {
        console.error('Cleanup failed:', error)
        this.$toast?.error('Cleanup failed')
      } finally {
        this.cleaning = false
      }
    },

    formatDate(date) {
      return new Date(date).toLocaleDateString()
    },

    formatTime(date) {
      return new Date(date).toLocaleTimeString()
    }
  }
}
</script>

<style scoped>
.admin-activity-logs {
  padding: 20px;
  background: #f8fafc;
  min-height: 100vh;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  background: white;
  padding: 25px 30px;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.header-content h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 8px 0;
  display: flex;
  align-items: center;
  gap: 12px;
}

.header-content h1 i {
  color: #3b82f6;
  font-size: 1.8rem;
}

.header-content p {
  color: #6b7280;
  margin: 0;
  font-size: 1rem;
}

.header-actions {
  display: flex;
  gap: 12px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  transition: all 0.2s ease;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, #3b82f6, #1d4ed8);
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
}

.stat-card .stat-content {
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  width: 64px;
  height: 64px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  font-size: 1.5rem;
  box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.3);
}

.stat-icon.critical {
  background: linear-gradient(135deg, #ef4444, #dc2626);
  box-shadow: 0 4px 14px 0 rgba(239, 68, 68, 0.3);
}

.stat-icon.warning {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  box-shadow: 0 4px 14px 0 rgba(245, 158, 11, 0.3);
}

.stat-content > div h3 {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.stat-content > div p {
  font-size: 1rem;
  font-weight: 500;
  color: #374151;
  margin: 0 0 4px 0;
}

.stat-content > div small {
  font-size: 0.875rem;
  color: #6b7280;
}

.filter-section {
  background: white;
  padding: 25px 30px;
  border-radius: 12px;
  margin-bottom: 20px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
}

.filter-row {
  display: flex;
  gap: 20px;
  align-items: end;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.filter-row:last-child {
  margin-bottom: 0;
}

.filter-group {
  display: flex;
  flex-direction: column;
  min-width: 180px;
  flex: 1;
}

.filter-group label {
  font-weight: 600;
  margin-bottom: 8px;
  color: #374151;
  font-size: 0.875rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.filter-group input,
.filter-group select {
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  background: white;
  color: #374151;
}

.filter-group input:focus,
.filter-group select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-actions {
  display: flex;
  gap: 12px;
  margin-top: 8px;
}

.content-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  overflow: hidden;
}

.table-container {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
}

.data-table thead {
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  border-bottom: 2px solid #e5e7eb;
}

.data-table th {
  padding: 16px 20px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-size: 0.75rem;
}

.data-table td {
  padding: 16px 20px;
  border-bottom: 1px solid #f3f4f6;
  vertical-align: top;
}

.log-row {
  transition: all 0.2s ease;
  position: relative;
}

.log-row:hover {
  background: linear-gradient(135deg, #fafbff, #f0f4ff);
}

.log-row.severity-critical {
  border-left: 4px solid #ef4444;
}

.log-row.severity-critical:hover {
  background: linear-gradient(135deg, #fef2f2, #fecaca);
}

.log-row.severity-warning {
  border-left: 4px solid #f59e0b;
}

.log-row.severity-warning:hover {
  background: linear-gradient(135deg, #fffbeb, #fed7aa);
}

.log-row.severity-success {
  border-left: 4px solid #10b981;
}

.log-row.severity-success:hover {
  background: linear-gradient(135deg, #f0fdf4, #bbf7d0);
}

.time-cell {
  white-space: nowrap;
  min-width: 140px;
}

.time-cell .date {
  display: block;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 2px;
}

.time-cell .time {
  display: block;
  font-size: 0.75rem;
  color: #6b7280;
  font-family: monospace;
}

.user-cell {
  min-width: 160px;
}

.user-cell .user-info {
  display: flex;
  flex-direction: column;
}

.user-cell .user-info strong {
  color: #1f2937;
  font-weight: 600;
  margin-bottom: 2px;
}

.user-cell .user-info small {
  color: #6b7280;
  font-size: 0.75rem;
}

.system-user {
  font-style: italic;
  color: #6b7280;
  font-weight: 500;
}

.action-badge, .severity-badge {
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: inline-block;
}

.action-badge {
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  color: #1e40af;
  border: 1px solid #93c5fd;
}

.severity-badge.severity-critical {
  background: linear-gradient(135deg, #fecaca, #fca5a5);
  color: #991b1b;
  border: 1px solid #f87171;
}

.severity-badge.severity-warning {
  background: linear-gradient(135deg, #fed7aa, #fdba74);
  color: #92400e;
  border: 1px solid #fb923c;
}

.severity-badge.severity-success {
  background: linear-gradient(135deg, #bbf7d0, #86efac);
  color: #065f46;
  border: 1px solid #4ade80;
}

.severity-badge.severity-info {
  background: linear-gradient(135deg, #dbeafe, #bfdbfe);
  color: #1e40af;
  border: 1px solid #93c5fd;
}

.description-cell {
  max-width: 300px;
}

.description-cell p {
  margin: 0 0 8px 0;
  color: #374151;
  line-height: 1.5;
}

.description-cell small {
  color: #6b7280;
  font-weight: 500;
}

.changes-cell .btn {
  padding: 6px 12px;
  font-size: 0.75rem;
}

.ip-cell {
  font-family: monospace;
  color: #6b7280;
  font-size: 0.8rem;
}

.pagination {
  padding: 20px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #f8fafc;
  border-top: 1px solid #e5e7eb;
}

.page-info {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 500;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.875rem;
  text-decoration: none;
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
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transition: left 0.5s;
}

.btn:hover::before {
  left: 100%;
}

.btn-primary {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.3);
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #2563eb, #1e40af);
  transform: translateY(-1px);
  box-shadow: 0 6px 20px 0 rgba(59, 130, 246, 0.4);
}

.btn-secondary {
  background: linear-gradient(135deg, #6b7280, #4b5563);
  color: white;
  box-shadow: 0 4px 14px 0 rgba(107, 114, 128, 0.3);
}

.btn-secondary:hover {
  background: linear-gradient(135deg, #4b5563, #374151);
  transform: translateY(-1px);
}

.btn-warning {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
  box-shadow: 0 4px 14px 0 rgba(245, 158, 11, 0.3);
}

.btn-warning:hover {
  background: linear-gradient(135deg, #d97706, #b45309);
  transform: translateY(-1px);
}

.btn-outline {
  background: white;
  color: #3b82f6;
  border: 2px solid #3b82f6;
}

.btn-outline:hover {
  background: #3b82f6;
  color: white;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

.btn-sm {
  padding: 6px 12px;
  font-size: 0.75rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  backdrop-filter: blur(4px);
}

.modal {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 600px;
  max-height: 80vh;
  overflow-y: auto;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  border: 1px solid #e5e7eb;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 30px;
  border-bottom: 1px solid #e5e7eb;
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1f2937;
}

.btn-close {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 8px;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.btn-close:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 30px;
}

.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 20px 30px;
  border-top: 1px solid #e5e7eb;
  background: #f8fafc;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-weight: 600;
  margin-bottom: 8px;
  color: #374151;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.875rem;
  transition: all 0.2s ease;
  background: white;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.date-range {
  display: flex;
  align-items: center;
  gap: 12px;
}

.input-group {
  display: flex;
  align-items: center;
  gap: 12px;
}

.alert {
  padding: 16px 20px;
  border-radius: 8px;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}

.alert-warning {
  background: linear-gradient(135deg, #fef3c7, #fde68a);
  border: 1px solid #f59e0b;
  color: #92400e;
}

.changes-detail h4 {
  margin-bottom: 20px;
  color: #1f2937;
  font-size: 1.125rem;
  font-weight: 600;
}

.changes-list {
  space-y: 12px;
}

.change-item {
  margin-bottom: 12px;
  padding: 16px;
  background: linear-gradient(135deg, #f8fafc, #f1f5f9);
  border: 1px solid #e5e7eb;
  border-radius: 8px;
}

.change-item strong {
  color: #374151;
  font-weight: 600;
}

.change-values {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 8px;
  flex-wrap: wrap;
}

.old-value {
  color: #dc2626;
  text-decoration: line-through;
  font-family: monospace;
  background: #fef2f2;
  padding: 4px 8px;
  border-radius: 4px;
  border: 1px solid #fecaca;
}

.new-value {
  color: #059669;
  font-weight: 600;
  font-family: monospace;
  background: #f0fdf4;
  padding: 4px 8px;
  border-radius: 4px;
  border: 1px solid #bbf7d0;
}

.loading-state, .empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #6b7280;
}

.loading-state i {
  font-size: 48px;
  margin-bottom: 20px;
  color: #3b82f6;
}

.empty-state i {
  font-size: 64px;
  margin-bottom: 20px;
  color: #d1d5db;
}

.empty-state h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }

  .filter-row {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-group {
    min-width: auto;
  }
}

@media (max-width: 768px) {
  .admin-activity-logs {
    padding: 15px;
  }

  .page-header {
    flex-direction: column;
    gap: 20px;
    align-items: stretch;
  }

  .header-actions {
    justify-content: center;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .table-container {
    font-size: 0.75rem;
  }

  .data-table th,
  .data-table td {
    padding: 12px 16px;
  }

  .modal {
    width: 95%;
    margin: 20px;
  }

  .modal-header,
  .modal-body,
  .modal-footer {
    padding: 20px;
  }
}
</style>
