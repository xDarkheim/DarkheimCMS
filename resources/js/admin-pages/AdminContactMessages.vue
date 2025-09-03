<template>
  <div class="admin-contact-messages">
    <div class="page-header">
      <div class="header-content">
        <h1><i class="fas fa-envelope"></i> Contact Messages</h1>
        <p>View and manage customer inquiries</p>
      </div>
      <div class="stats-summary">
        <div class="stat-item">
          <span class="stat-number">{{ stats?.unread || 0 }}</span>
          <span class="stat-label">Unread</span>
        </div>
        <div class="stat-item">
          <span class="stat-number">{{ stats?.today || 0 }}</span>
          <span class="stat-label">Today</span>
        </div>
      </div>
    </div>

    <!-- Messages List -->
    <div class="content-card">
      <div class="table-header">
        <h3>All Messages</h3>
        <div class="table-controls">
          <select v-model="filterStatus" @change="loadMessages(1)" class="filter-select">
            <option value="">All Messages</option>
            <option value="unread">Unread Only</option>
            <option value="read">Read Only</option>
          </select>
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input
              type="text"
              v-model="searchQuery"
              @input="handleSearch"
              placeholder="Search messages..."
              class="search-input"
            >
          </div>
        </div>
      </div>

      <div class="messages-list">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading messages...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error && !loading" class="error-state">
          <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <h3>Failed to Load Messages</h3>
          <p>{{ error }}</p>
          <button @click="loadMessages()" class="btn btn-primary">
            <i class="fas fa-redo"></i>
            Retry
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!loading && messages.length === 0" class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-inbox"></i>
          </div>
          <h3>No Messages Found</h3>
          <p>{{ filterStatus === 'unread' ? 'No unread messages at the moment.' : 'No contact messages have been received yet.' }}</p>
        </div>

        <!-- Messages Table -->
        <div v-else class="messages-table">
          <table>
            <thead>
              <tr>
                <th>Status</th>
                <th>Type</th>
                <th>Contact Info</th>
                <th>Service/Position</th>
                <th>Message Preview</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="message in messages"
                :key="message.id"
                :class="{ 'unread-row': !message.is_read }"
              >
                <td>
                  <span
                    class="status-badge"
                    :class="message.is_read ? 'status-read' : 'status-unread'"
                  >
                    <i :class="message.is_read ? 'fas fa-envelope-open' : 'fas fa-envelope'"></i>
                    {{ message.is_read ? 'Read' : 'Unread' }}
                  </span>
                </td>
                <td>
                  <span
                    class="type-badge"
                    :class="message.message_type === 'job_application' ? 'type-job' : 'type-general'"
                  >
                    <i :class="message.message_type === 'job_application' ? 'fas fa-briefcase' : 'fas fa-envelope'"></i>
                    {{ message.message_type === 'job_application' ? 'Job App' : 'General' }}
                  </span>
                </td>
                <td>
                  <div class="contact-info">
                    <div class="contact-name">{{ message.name }}</div>
                    <div class="contact-email">{{ message.email }}</div>
                    <div v-if="message.company" class="contact-company">{{ message.company }}</div>
                  </div>
                </td>
                <td>
                  <span v-if="message.message_type === 'job_application' && message.position_interest" class="service-tag job-position">
                    {{ message.position_interest }}
                  </span>
                  <span v-else-if="message.service" class="service-tag">
                    {{ formatService(message.service) }}
                  </span>
                  <span v-else class="text-muted">-</span>
                </td>
                <td>
                  <div class="message-preview">
                    {{ truncateText(message.message, 100) }}
                  </div>
                </td>
                <td>
                  <div class="date-info">
                    <div class="date">{{ formatDate(message.created_at) }}</div>
                    <div class="time">{{ formatTime(message.created_at) }}</div>
                  </div>
                </td>
                <td>
                  <div class="action-buttons">
                    <button
                      @click="viewMessage(message)"
                      class="btn btn-sm btn-outline"
                      title="View Message"
                    >
                      <i class="fas fa-eye"></i>
                    </button>

                    <button
                      v-if="message.resume_file"
                      @click="downloadResume(message)"
                      class="btn btn-sm btn-info"
                      title="Download Resume"
                      :disabled="processingIds.includes(message.id)"
                    >
                      <i class="fas fa-download"></i>
                    </button>

                    <button
                      v-if="!message.is_read"
                      @click="markAsRead(message)"
                      class="btn btn-sm btn-success"
                      title="Mark as Read"
                      :disabled="processingIds.includes(message.id)"
                    >
                      <i class="fas fa-check"></i>
                    </button>

                    <button
                      @click="deleteMessage(message)"
                      class="btn btn-sm btn-danger"
                      title="Delete Message"
                      :disabled="processingIds.includes(message.id)"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pagination && pagination.last_page > 1" class="pagination">
          <button
            @click="loadMessages(pagination.current_page - 1)"
            :disabled="pagination.current_page <= 1"
            class="btn btn-outline"
          >
            <i class="fas fa-chevron-left"></i>
            Previous
          </button>

          <span class="pagination-info">
            Page {{ pagination.current_page }} of {{ pagination.last_page }}
          </span>

          <button
            @click="loadMessages(pagination.current_page + 1)"
            :disabled="pagination.current_page >= pagination.last_page"
            class="btn btn-outline"
          >
            Next
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Message Detail Modal -->
    <div v-if="selectedMessage" class="modal-overlay" @click="closeModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>
            <i :class="selectedMessage.message_type === 'job_application' ? 'fas fa-briefcase' : 'fas fa-envelope'"></i>
            {{ selectedMessage.message_type === 'job_application' ? 'Job Application' : 'Message' }} from {{ selectedMessage.name }}
          </h3>
          <button @click="closeModal" class="btn btn-ghost btn-sm">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="modal-body">
          <div class="message-details">
            <div class="detail-row">
              <label>Type:</label>
              <span>
                <span
                  class="type-badge"
                  :class="selectedMessage.message_type === 'job_application' ? 'type-job' : 'type-general'"
                >
                  <i :class="selectedMessage.message_type === 'job_application' ? 'fas fa-briefcase' : 'fas fa-envelope'"></i>
                  {{ selectedMessage.message_type === 'job_application' ? 'Job Application' : 'General Inquiry' }}
                </span>
              </span>
            </div>

            <div class="detail-row">
              <label>Name:</label>
              <span>{{ selectedMessage.name }}</span>
            </div>

            <div class="detail-row">
              <label>Email:</label>
              <span>
                <a :href="`mailto:${selectedMessage.email}`">{{ selectedMessage.email }}</a>
              </span>
            </div>

            <div v-if="selectedMessage.phone" class="detail-row">
              <label>Phone:</label>
              <span>{{ selectedMessage.phone }}</span>
            </div>

            <div v-if="selectedMessage.company" class="detail-row">
              <label>Company:</label>
              <span>{{ selectedMessage.company }}</span>
            </div>

            <!-- Job Application Specific Fields -->
            <template v-if="selectedMessage.message_type === 'job_application'">
              <div v-if="selectedMessage.position_interest" class="detail-row">
                <label>Position of Interest:</label>
                <span>{{ selectedMessage.position_interest }}</span>
              </div>

              <div v-if="selectedMessage.portfolio_url" class="detail-row">
                <label>Portfolio:</label>
                <span>
                  <a :href="selectedMessage.portfolio_url" target="_blank">{{ selectedMessage.portfolio_url }}</a>
                </span>
              </div>

              <div v-if="selectedMessage.experience_summary" class="detail-row">
                <label>Experience Summary:</label>
                <div class="detail-text">{{ selectedMessage.experience_summary }}</div>
              </div>

              <div v-if="selectedMessage.availability" class="detail-row">
                <label>Availability:</label>
                <span>{{ formatAvailability(selectedMessage.availability) }}</span>
              </div>

              <div v-if="selectedMessage.salary_expectation" class="detail-row">
                <label>Salary Expectation:</label>
                <span>${{ Number(selectedMessage.salary_expectation).toLocaleString() }}</span>
              </div>

              <div v-if="selectedMessage.resume_file" class="detail-row">
                <label>Resume:</label>
                <span>
                  <button @click="downloadResume(selectedMessage)" class="btn btn-sm btn-info">
                    <i class="fas fa-download"></i>
                    Download Resume
                  </button>
                </span>
              </div>
            </template>

            <!-- General Inquiry Fields -->
            <template v-else>
              <div v-if="selectedMessage.service" class="detail-row">
                <label>Service:</label>
                <span>{{ formatService(selectedMessage.service) }}</span>
              </div>

              <div v-if="selectedMessage.budget" class="detail-row">
                <label>Budget:</label>
                <span>{{ formatBudget(selectedMessage.budget) }}</span>
              </div>
            </template>

            <div class="detail-row">
              <label>Date:</label>
              <span>{{ formatFullDate(selectedMessage.created_at) }}</span>
            </div>

            <div class="detail-row message-content">
              <label>{{ selectedMessage.message_type === 'job_application' ? 'Cover Letter:' : 'Message:' }}</label>
              <div class="message-text">{{ selectedMessage.message }}</div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button
            v-if="selectedMessage.resume_file"
            @click="downloadResume(selectedMessage)"
            class="btn btn-info"
            :disabled="processingIds.includes(selectedMessage.id)"
          >
            <i class="fas fa-download"></i>
            Download Resume
          </button>

          <button
            v-if="!selectedMessage.is_read"
            @click="markAsRead(selectedMessage)"
            class="btn btn-success"
            :disabled="processingIds.includes(selectedMessage.id)"
          >
            <i class="fas fa-check"></i>
            Mark as Read
          </button>

          <button
            @click="deleteMessage(selectedMessage)"
            class="btn btn-danger"
            :disabled="processingIds.includes(selectedMessage.id)"
          >
            <i class="fas fa-trash"></i>
            Delete
          </button>

          <button @click="closeModal" class="btn btn-outline">
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { adminContactService } from '../admin-services/contactService.js'

export default {
  name: 'AdminContactMessages',
  setup() {
    // State
    const loading = ref(false)
    const error = ref('')
    const messages = ref([])
    const stats = ref(null)
    const pagination = ref(null)
    const selectedMessage = ref(null)
    const processingIds = ref([])

    // Filters
    const searchQuery = ref('')
    const filterStatus = ref('')
    const currentPage = ref(1)

    // Search debounce
    let searchTimeout = null

    // Methods
    const loadMessages = async (page = 1) => {
      try {
        loading.value = true
        error.value = ''

        const params = {
          page,
          search: searchQuery.value
        }

        // Add status filter based on filterStatus value
        if (filterStatus.value === 'unread') {
          params.status = 'unread'
        } else if (filterStatus.value === 'read') {
          params.status = 'read'
        }
        // If filterStatus is empty, don't add status param to get all messages

        const response = await adminContactService.getMessages(params)

        if (response.success) {
          messages.value = response.data.data
          pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            per_page: response.data.per_page,
            total: response.data.total,
            from: response.data.from,
            to: response.data.to
          }
        }
      } catch (err) {
        error.value = err.message || 'Failed to load messages'
        console.error('Error loading messages:', err)
      } finally {
        loading.value = false
      }
    }

    const loadStats = async () => {
      try {
        const response = await adminContactService.getStats()
        if (response.success) {
          stats.value = response.data
        }
      } catch (err) {
        console.error('Error loading stats:', err)
      }
    }

    const handleSearch = () => {
      if (searchTimeout) clearTimeout(searchTimeout)
      searchTimeout = setTimeout(() => {
        currentPage.value = 1
        loadMessages(1)
      }, 500)
    }

    const viewMessage = (message) => {
      selectedMessage.value = message
      // Automatically mark as read when viewing
      if (!message.is_read) {
        markAsRead(message)
      }
    }

    const closeModal = () => {
      selectedMessage.value = null
    }

    const markAsRead = async (message) => {
      if (message.is_read) return

      try {
        processingIds.value.push(message.id)
        const response = await adminContactService.markAsRead(message.id)

        if (response.success) {
          // Update the message in the list instead of removing it
          const messageIndex = messages.value.findIndex(m => m.id === message.id)
          if (messageIndex !== -1) {
            messages.value[messageIndex].is_read = true
          }

          // Update selected message if it's the same
          if (selectedMessage.value && selectedMessage.value.id === message.id) {
            selectedMessage.value.is_read = true
          }

          await loadStats() // Refresh stats

          // Only reload messages if we're filtering by unread to keep consistency
          if (filterStatus.value === 'unread') {
            // Small delay to ensure the user sees the status change
            setTimeout(() => {
              loadMessages(pagination.value?.current_page || 1)
            }, 1000)
          }
        }
      } catch (err) {
        console.error('Error marking message as read:', err)
        error.value = 'Failed to mark message as read'
      } finally {
        processingIds.value = processingIds.value.filter(id => id !== message.id)
      }
    }

    const deleteMessage = async (message) => {
      if (!confirm('Are you sure you want to delete this message? This action cannot be undone.')) {
        return
      }

      try {
        processingIds.value.push(message.id)
        const response = await adminContactService.deleteMessage(message.id)

        if (response.success) {
          messages.value = messages.value.filter(m => m.id !== message.id)
          await loadStats() // Refresh stats

          if (selectedMessage.value && selectedMessage.value.id === message.id) {
            closeModal()
          }
        }
      } catch (err) {
        console.error('Error deleting message:', err)
        error.value = 'Failed to delete message'
      } finally {
        processingIds.value = processingIds.value.filter(id => id !== message.id)
      }
    }

    const downloadResume = async (message) => {
      try {
        processingIds.value.push(message.id)
        window.open(`/api/admin/contact-messages/${message.id}/resume`, '_blank')
      } catch (err) {
        console.error('Error downloading resume:', err)
        error.value = 'Failed to download resume'
      } finally {
        processingIds.value = processingIds.value.filter(id => id !== message.id)
      }
    }

    const formatAvailability = (availability) => {
      const availabilities = {
        'immediate': 'Immediate',
        '2weeks': '2 weeks',
        '1month': '1 month',
        '2months': '2 months',
        'negotiable': 'Negotiable'
      }
      return availabilities[availability] || availability
    }

    // Utility methods
    const formatService = (service) => {
      const services = {
        'web-development': 'Web Development',
        'mobile-development': 'Mobile Development',
        'ui-ux-design': 'UI/UX Design',
        'ecommerce': 'E-commerce',
        'consultation': 'Consultation',
        'other': 'Other'
      }
      return services[service] || service
    }

    const formatBudget = (budget) => {
      const budgets = {
        'under-5k': 'Under $5,000',
        '5k-10k': '$5,000 - $10,000',
        '10k-25k': '$10,000 - $25,000',
        '25k-50k': '$25,000 - $50,000',
        'over-50k': 'Over $50,000'
      }
      return budgets[budget] || budget
    }

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString()
    }

    const formatTime = (dateString) => {
      return new Date(dateString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
    }

    const formatFullDate = (dateString) => {
      return new Date(dateString).toLocaleString()
    }

    const truncateText = (text, length) => {
      if (text.length <= length) return text
      return text.substring(0, length) + '...'
    }

    // Lifecycle
    onMounted(() => {
      loadMessages()
      loadStats()
    })

    return {
      // State
      loading,
      error,
      messages,
      stats,
      pagination,
      selectedMessage,
      processingIds,

      // Filters
      searchQuery,
      filterStatus,
      currentPage,

      // Methods
      loadMessages,
      handleSearch,
      viewMessage,
      closeModal,
      markAsRead,
      deleteMessage,
      downloadResume,
      formatService,
      formatBudget,
      formatAvailability,
      formatDate,
      formatTime,
      formatFullDate,
      truncateText
    }
  }
}
</script>

<style lang="scss" scoped>
.admin-contact-messages {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;

  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;

    .header-content {
      h1 {
        color: #2c3e50;
        margin-bottom: 0.5rem;
        font-size: 2rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.75rem;

        i {
          color: #3498db;
        }
      }

      p {
        color: #7f8c8d;
        margin: 0;
      }
    }

    .stats-summary {
      display: flex;
      gap: 2rem;

      .stat-item {
        text-align: center;

        .stat-number {
          display: block;
          font-size: 1.5rem;
          font-weight: 600;
          color: #3498db;
        }

        .stat-label {
          font-size: 0.875rem;
          color: #7f8c8d;
        }
      }
    }
  }

  .content-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    border: 1px solid #e2e8f0;
    overflow: hidden;
  }

  .table-header {
    padding: 2rem 2rem 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f1f3f4;

    h3 {
      margin: 0;
      font-size: 1.25rem;
      font-weight: 600;
      color: #2c3e50;
    }
  }

  .messages-list {
    padding: 2rem;
  }

  // Loading, Error and Empty States
  .loading-state,
  .error-state,
  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;
    color: #7f8c8d;

    .loading-spinner {
      width: 40px;
      height: 40px;
      border: 3px solid #f3f3f3;
      border-top: 3px solid #3498db;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 1rem;
    }

    .error-icon,
    .empty-icon {
      font-size: 3rem;
      color: #95a5a6;
      margin-bottom: 1rem;
    }

    h3 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #2c3e50;
      margin: 0 0 0.5rem 0;
    }

    p {
      font-size: 1rem;
      color: #7f8c8d;
      margin: 0 0 1.5rem 0;
      max-width: 400px;
    }
  }

  .error-state .error-icon {
    color: #e74c3c;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  // Filter and Search Controls
  .table-controls {
    display: flex;
    gap: 1rem;
    align-items: center;

    .filter-select {
      padding: 0.5rem 1rem;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      background: white;
      font-size: 0.9rem;
      color: #495057;

      &:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
      }
    }

    .search-box {
      position: relative;

      i {
        position: absolute;
        left: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        font-size: 0.9rem;
      }

      .search-input {
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
        font-size: 0.9rem;
        width: 250px;
        transition: all 0.2s ease;

        &:focus {
          outline: none;
          border-color: #3498db;
          background: white;
          box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        &::placeholder {
          color: #adb5bd;
        }
      }
    }
  }

  .messages-table {
    table {
      width: 100%;
      border-collapse: collapse;

      th {
        text-align: left;
        padding: 1rem;
        border-bottom: 2px solid #e2e8f0;
        font-weight: 600;
        color: #2c3e50;
      }

      td {
        padding: 1rem;
        border-bottom: 1px solid #f1f3f4;
        vertical-align: top;
      }

      .unread-row {
        background-color: rgba(52, 152, 219, 0.05);
      }
    }
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;

    &.status-read {
      background: #d4edda;
      color: #155724;
    }

    &.status-unread {
      background: #fff3cd;
      color: #856404;
    }
  }

  .type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;

    &.type-job {
      background: #e1f5fe;
      color: #01579b;
    }

    &.type-general {
      background: #f3e5f5;
      color: #6a1b9a;
    }
  }

  .contact-info {
    .contact-name {
      font-weight: 500;
      color: #2c3e50;
      margin-bottom: 0.25rem;
    }

    .contact-email {
      font-size: 0.875rem;
      color: #6c757d;
      margin-bottom: 0.25rem;
    }

    .contact-company {
      font-size: 0.75rem;
      color: #adb5bd;
    }
  }

  .service-tag {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: rgba(52, 152, 219, 0.1);
    color: #3498db;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;

    &.job-position {
      background: rgba(255, 193, 7, 0.1);
      color: #f39c12;
    }
  }

  .message-preview {
    font-size: 0.875rem;
    color: #6c757d;
    line-height: 1.4;
  }

  .date-info {
    .date {
      font-weight: 500;
      color: #2c3e50;
    }

    .time {
      font-size: 0.75rem;
      color: #6c757d;
      margin-top: 0.25rem;
    }
  }

  .action-buttons {
    display: flex;
    gap: 0.5rem;
  }

  // Pagination
  .pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 0;
    border-top: 1px solid #f1f3f4;
    margin-top: 2rem;

    .pagination-info {
      font-size: 0.9rem;
      color: #6c757d;
    }
  }

  // Buttons
  .btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: 1px solid transparent;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;

    &.btn-sm {
      padding: 0.375rem 0.75rem;
      font-size: 0.8rem;
    }

    &.btn-primary {
      background: #3498db;
      color: white;
      border-color: #3498db;

      &:hover {
        background: #2980b9;
        border-color: #2980b9;
      }
    }

    &.btn-success {
      background: #27ae60;
      color: white;
      border-color: #27ae60;

      &:hover {
        background: #229954;
        border-color: #229954;
      }
    }

    &.btn-danger {
      background: #e74c3c;
      color: white;
      border-color: #e74c3c;

      &:hover {
        background: #c0392b;
        border-color: #c0392b;
      }
    }

    &.btn-outline {
      background: white;
      color: #6c757d;
      border-color: #dee2e6;

      &:hover {
        background: #f8f9fa;
        color: #3498db;
        border-color: #3498db;
      }
    }

    &.btn-ghost {
      background: transparent;
      border: none;
      color: #6c757d;

      &:hover {
        background: #f8f9fa;
        color: #495057;
      }
    }

    &:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      pointer-events: none;
    }
  }

  // Modal
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 2rem;
  }

  .modal {
    background: white;
    border-radius: 12px;
    max-width: 600px;
    width: 100%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);

    .modal-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      border-bottom: 1px solid #e2e8f0;

      h3 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
        color: #2c3e50;

        i {
          color: #3498db;
          margin-right: 0.5rem;
        }
      }
    }

    .modal-body {
      padding: 1.5rem;
    }

    .modal-footer {
      padding: 1.5rem;
      border-top: 1px solid #e2e8f0;
      display: flex;
      gap: 1rem;
      justify-content: flex-end;
    }
  }

  .message-details {
    .detail-row {
      display: flex;
      margin-bottom: 1rem;

      label {
        min-width: 100px;
        font-weight: 500;
        color: #2c3e50;
      }

      span, a {
        color: #7f8c8d;
      }

      a {
        color: #3498db;
        text-decoration: none;

        &:hover {
          text-decoration: underline;
        }
      }

      &.message-content {
        flex-direction: column;

        label {
          margin-bottom: 0.5rem;
        }

        .message-text {
          background: #f8f9fa;
          padding: 1rem;
          border-radius: 8px;
          line-height: 1.6;
          white-space: pre-wrap;
          color: #2c3e50;
        }
      }
    }
  }

  // Utility classes
  .text-muted {
    color: #95a5a6;
  }
}
</style>
