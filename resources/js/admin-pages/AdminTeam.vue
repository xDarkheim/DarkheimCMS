<template>
  <div class="admin-team">
    <div class="admin-header">
      <h1>Team Management</h1>
      <button @click="showCreateModal" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add Team Member
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ teamMembers.length }}</div>
          <div class="stat-label">Total Members</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-eye"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ visibleMembers }}</div>
          <div class="stat-label">Visible on Website</div>
        </div>
      </div>
    </div>

    <!-- Team Members Table -->
    <div class="table-container">
      <table class="admin-table" v-if="teamMembers.length > 0">
        <thead>
          <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Position</th>
            <th>Department</th>
            <th>Status</th>
            <th>Visible</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="member in teamMembers" :key="member.id">
            <td class="avatar-cell">
              <img :src="member.avatar" :alt="member.name" class="avatar-small">
            </td>
            <td class="member-name">
              <strong>{{ member.name }}</strong>
              <small v-if="member.email">{{ member.email }}</small>
            </td>
            <td>{{ member.position }}</td>
            <td>{{ member.department }}</td>
            <td>
              <span class="status-badge" :class="member.status">
                {{ member.status.charAt(0).toUpperCase() + member.status.slice(1) }}
              </span>
            </td>
            <td>
              <span class="visibility-badge" :class="member.show_on_website ? 'visible' : 'hidden'">
                {{ member.show_on_website ? 'Yes' : 'No' }}
              </span>
            </td>
            <td class="actions">
              <button @click.stop="toggleVisibility(member)" class="btn-icon" :title="member.show_on_website ? 'Hide from website' : 'Show on website'">
                <i :class="member.show_on_website ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
              <button @click.stop="editMember(member)" class="btn-icon" title="Edit">
                <i class="fas fa-edit"></i>
              </button>
              <button @click.stop="deleteMember(member)" class="btn-icon danger" title="Delete">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <i class="fas fa-users"></i>
        <h3>No Team Members Yet</h3>
        <p>Start building your team by adding your first member</p>
        <button @click="showCreateModal" class="btn btn-primary" style="margin-top: 16px;">
          <i class="fas fa-plus"></i>
          Add First Member
        </button>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h2>{{ isEditing ? 'Edit Team Member' : 'Add Team Member' }}</h2>
          <button @click="closeModal" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveMember" class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label>Name *</label>
              <input v-model="form.name" type="text" required>
            </div>

            <div class="form-group">
              <label>Position *</label>
              <input v-model="form.position" type="text" required>
            </div>

            <div class="form-group">
              <label>Department *</label>
              <input v-model="form.department" type="text" required>
            </div>

            <div class="form-group">
              <label>Status *</label>
              <select v-model="form.status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="on-leave">On Leave</option>
              </select>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input v-model="form.email" type="email">
            </div>

            <div class="form-group">
              <label>Joined Date</label>
              <input v-model="form.joined_date" type="date">
            </div>

            <div class="form-group full-width">
              <label>Avatar</label>
              <input @change="handleAvatarChange" type="file" accept="image/*">
              <div v-if="avatarPreview || currentAvatarUrl" class="avatar-preview">
                <img :src="avatarPreview || currentAvatarUrl" alt="Preview">
              </div>
            </div>

            <div class="form-group full-width">
              <label>Bio *</label>
              <textarea v-model="form.bio" rows="4" required></textarea>
            </div>

            <div class="form-group full-width">
              <label>Skills (comma-separated)</label>
              <input v-model="skillsInput" type="text" placeholder="JavaScript, React, Node.js">
            </div>

            <!-- Social Links -->
            <div class="form-group full-width">
              <label>Social Links</label>
              <div class="social-inputs">
                <div class="social-input">
                  <i class="fab fa-linkedin"></i>
                  <input v-model="socialLinks.linkedin" type="url" placeholder="LinkedIn URL">
                </div>
                <div class="social-input">
                  <i class="fab fa-twitter"></i>
                  <input v-model="socialLinks.twitter" type="url" placeholder="Twitter URL">
                </div>
                <div class="social-input">
                  <i class="fab fa-github"></i>
                  <input v-model="socialLinks.github" type="url" placeholder="GitHub URL">
                </div>
                <div class="social-input">
                  <i class="fab fa-dribbble"></i>
                  <input v-model="socialLinks.dribbble" type="url" placeholder="Dribbble URL">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input v-model="form.show_on_website" type="checkbox">
                Show on Website
              </label>
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              <i class="fas fa-save"></i>
              {{ loading ? 'Saving...' : 'Save Member' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

export default {
  name: 'AdminTeam',
  setup() {
    const teamMembers = ref([])
    const showModal = ref(false)
    const isEditing = ref(false)
    const loading = ref(false)
    const skillsInput = ref('')
    const avatarFile = ref(null)
    const avatarPreview = ref(null)
    const currentAvatarUrl = ref('')

    // Get auth token from localStorage
    const getAuthHeaders = () => {
      const token = localStorage.getItem('admin_token')
      return token ? { Authorization: `Bearer ${token}` } : {}
    }

    const form = ref({
      name: '',
      position: '',
      department: '',
      bio: '',
      email: '',
      skills: [],
      social_links: {},
      status: 'active',
      joined_date: null,
      show_on_website: true
    })

    const socialLinks = ref({
      linkedin: '',
      twitter: '',
      github: '',
      dribbble: ''
    })

    const visibleMembers = computed(() =>
      teamMembers.value.filter(member => member.show_on_website).length
    )

    const loadTeamMembers = async () => {
      try {
        const response = await axios.get('/api/team', {
          headers: getAuthHeaders()
        })
        teamMembers.value = response.data.data || []
      } catch (error) {
        console.error('Failed to load team members:', error)
      }
    }

    const showCreateModal = () => {
      resetForm()
      isEditing.value = false
      currentAvatarUrl.value = ''
      showModal.value = true
    }

    const formatDateForInput = (value) => {
      if (!value) return null
      // Accept Date, ISO string, or YYYY-MM-DD
      try {
        if (typeof value === 'string') {
          if (/^\d{4}-\d{2}-\d{2}$/.test(value)) return value
          const d = new Date(value)
          if (!isNaN(d.getTime())) {
            const yyyy = d.getFullYear()
            const mm = String(d.getMonth() + 1).padStart(2, '0')
            const dd = String(d.getDate()).padStart(2, '0')
            return `${yyyy}-${mm}-${dd}`
          }
          return null
        }
        if (value instanceof Date) {
          const yyyy = value.getFullYear()
          const mm = String(value.getMonth() + 1).padStart(2, '0')
          const dd = String(value.getDate()).padStart(2, '0')
          return `${yyyy}-${mm}-${dd}`
        }
      } catch (_) { return null }
      return null
    }

    const editMember = (member) => {
      console.log('editMember called with:', member); // Отладка

      Object.keys(form.value).forEach(key => {
        if (Object.prototype.hasOwnProperty.call(member, key)) {
          form.value[key] = member[key]
        }
      })

      // Важно: добавляем ID для правильного редактирования
      form.value.id = member.id

      // Normalize joined_date for input type=date
      form.value.joined_date = formatDateForInput(member.joined_date)

      skillsInput.value = member.skills ? member.skills.join(', ') : ''

      // Set social links
      if (member.social_links) {
        Object.keys(socialLinks.value).forEach(platform => {
          socialLinks.value[platform] = member.social_links[platform] || ''
        })
      }

      // Show current avatar if exists
      currentAvatarUrl.value = member.avatar || ''
      avatarPreview.value = member.avatar || null

      isEditing.value = true
      showModal.value = true

      console.log('Modal should be shown:', showModal.value, 'isEditing:', isEditing.value); // Отладка
    }

    const handleAvatarChange = (event) => {
      const file = event.target.files[0]
      if (file) {
        avatarFile.value = file
        const reader = new FileReader()
        reader.onload = (e) => {
          avatarPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
      }
    }

    const saveMember = async () => {
      try {
        loading.value = true

        // Process skills
        form.value.skills = skillsInput.value
          .split(',')
          .map(skill => skill.trim())
          .filter(skill => skill.length > 0)

        // Process social links
        form.value.social_links = Object.entries(socialLinks.value)
          .filter(([, url]) => (url || '').trim() !== '')
          .reduce((acc, [platform, url]) => {
            acc[platform] = url
            return acc
          }, {})

        // Helper to append only when value is present (and valid)
        const appendIfPresent = (fd, key, value) => {
          if (value === undefined || value === null) return
          if (typeof value === 'string' && value.trim() === '') return
          fd.append(key, value)
        }

        // Create FormData for file upload
        const formData = new FormData()

        // Required fields (always append)
        formData.append('name', form.value.name)
        formData.append('position', form.value.position)
        formData.append('department', form.value.department)
        formData.append('bio', form.value.bio)
        formData.append('status', form.value.status)

        // Optional fields (append only if present)
        appendIfPresent(formData, 'email', form.value.email)
        // Ensure boolean is sent as 1/0 for robustness
        formData.append('show_on_website', form.value.show_on_website ? '1' : '0')
        appendIfPresent(formData, 'joined_date', form.value.joined_date)

        // Arrays/objects: send as JSON strings if non-empty
        if (Array.isArray(form.value.skills) && form.value.skills.length > 0) {
          formData.append('skills', JSON.stringify(form.value.skills))
        }
        if (form.value.social_links && Object.keys(form.value.social_links).length > 0) {
          formData.append('social_links', JSON.stringify(form.value.social_links))
        }

        if (avatarFile.value) {
          formData.append('avatar', avatarFile.value)
        }

        let response
        if (isEditing.value) {
          // Для обновления используем PUT метод
          formData.append('_method', 'PUT')
          response = await axios.post(`/api/admin/team/${form.value.id}`, formData, {
            headers: {
              ...getAuthHeaders(),
              'Content-Type': 'multipart/form-data'
            }
          })
        } else {
          // Для создания используем POST
          response = await axios.post('/api/admin/team', formData, {
            headers: {
              ...getAuthHeaders(),
              'Content-Type': 'multipart/form-data'
            }
          })
        }

        closeModal()
        loadTeamMembers()
      } catch (error) {
        console.error('Failed to save team member:', error)
        // Build detailed validation message if available
        let message = error.response?.data?.message || error.message
        const errors = error.response?.data?.errors
        if (errors && typeof errors === 'object') {
          const details = Object.values(errors).flat().join(', ')
          if (details) message += `: ${details}`
        }
        alert('Error saving team member: ' + message)
      } finally {
        loading.value = false
      }
    }

    const deleteMember = async (member) => {
      if (!confirm(`Are you sure you want to delete "${member.name}"?`)) return

      try {
        console.log('Deleting team member:', member.id, member.name)
        const response = await axios.delete(`/api/admin/team/${member.id}`, {
          headers: getAuthHeaders()
        })
        console.log('Delete response:', response.data)
        loadTeamMembers()
      } catch (error) {
        console.error('Failed to delete team member:', error)
        console.error('Error response:', error.response?.data)
        alert('Error deleting team member: ' + (error.response?.data?.message || error.message))
      }
    }

    const toggleVisibility = async (member) => {
      try {
        await axios.post(`/api/admin/team/${member.id}/toggle-visible`, {}, {
          headers: getAuthHeaders()
        })
        // Optimistically update the local member data
        member.show_on_website = !member.show_on_website
      } catch (error) {
        console.error('Failed to toggle visibility:', error)
        alert('Error toggling visibility: ' + (error.response?.data?.message || error.message))
      }
    }

    const closeModal = () => {
      showModal.value = false
      resetForm()
    }

    const resetForm = () => {
      Object.assign(form.value, {
        name: '',
        position: '',
        department: '',
        bio: '',
        email: '',
        skills: [],
        social_links: {},
        status: 'active',
        joined_date: null,
        show_on_website: true
      })
      skillsInput.value = ''
      avatarFile.value = null
      avatarPreview.value = null
      currentAvatarUrl.value = ''
      Object.keys(socialLinks.value).forEach(key => {
        socialLinks.value[key] = ''
      })
    }

    onMounted(loadTeamMembers)

    return {
      teamMembers,
      visibleMembers,
      showModal,
      isEditing,
      loading,
      form,
      skillsInput,
      socialLinks,
      avatarPreview,
      currentAvatarUrl,
      showCreateModal,
      editMember,
      saveMember,
      deleteMember,
      closeModal,
      handleAvatarChange,
      formatDateForInput,
      toggleVisibility
    }
  }
}
</script>

<style scoped>
.admin-team {
  padding: 20px;
}

.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.admin-header h1 {
  font-size: 24px;
  margin: 0;
}

.btn-primary {
  background-color: #007bff;
  color: #fff;
  border: none;
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.stat-card {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 15px;
}

.stat-icon {
  font-size: 32px;
  color: #007bff;
}

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 24px;
  font-weight: bold;
  margin-bottom: 5px;
}

.stat-label {
  font-size: 14px;
  color: #666;
}

.table-container {
  background: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  border: 1px solid #e5e7eb;
}

.admin-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
}

.admin-table th {
  padding: 16px 20px;
  text-align: left;
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  font-weight: 600;
  font-size: 13px;
  color: #374151;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 2px solid #e5e7eb;
  position: sticky;
  top: 0;
  z-index: 10;
}

.admin-table td {
  padding: 16px 20px;
  text-align: left;
  border-bottom: 1px solid #f3f4f6;
  vertical-align: middle;
  transition: background-color 0.2s ease;
}

.admin-table tr:hover td {
  background: #f9fafb;
}

.admin-table tr:last-child td {
  border-bottom: none;
}

.avatar-cell {
  width: 80px;
  padding-right: 12px;
}

.avatar-small {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  object-fit: cover;
  border: 2px solid #e5e7eb;
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.avatar-small:hover {
  transform: scale(1.05);
  border-color: #3b82f6;
}

.member-name {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 180px;
}

.member-name strong {
  font-weight: 600;
  color: #111827;
  font-size: 15px;
  margin-bottom: 2px;
}

.member-name small {
  color: #6b7280;
  font-size: 12px;
  font-weight: 400;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  min-width: 70px;
  justify-content: center;
}

.status-badge.active {
  background-color: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.status-badge.inactive {
  background-color: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}

.status-badge.on-leave {
  background-color: #fef3c7;
  color: #92400e;
  border: 1px solid #fde68a;
}

.visibility-badge {
  display: inline-flex;
  align-items: center;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  min-width: 60px;
  justify-content: center;
}

.visibility-badge.visible {
  background-color: #dbeafe;
  color: #1e40af;
  border: 1px solid #93c5fd;
}

.visibility-badge.hidden {
  background-color: #f3f4f6;
  color: #6b7280;
  border: 1px solid #d1d5db;
}

.actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  min-width: 140px;
}

.btn-icon {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 10px;
  cursor: pointer;
  border-radius: 8px;
  font-size: 14px;
  color: #64748b;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
}

.btn-icon:hover {
  background-color: #f1f5f9;
  border-color: #cbd5e1;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.btn-icon:active {
  transform: translateY(0);
}

.btn-icon i {
  font-size: 14px;
}

/* Специальные стили для кнопки видимости */
.btn-icon[title*="Show"]:hover,
.btn-icon[title*="Hide"]:hover {
  background-color: #dbeafe;
  border-color: #93c5fd;
  color: #1d4ed8;
}

/* Специальные стили для кнопки редактирования */
.btn-icon[title="Edit"]:hover {
  background-color: #fef3c7;
  border-color: #fde68a;
  color: #d97706;
}

/* Специальные стили для кнопки удаления */
.btn-icon.danger:hover {
  background-color: #fee2e2;
  border-color: #fca5a5;
  color: #dc2626;
}

/* Адаптивность таблицы */
@media (max-width: 1200px) {
  .admin-table th,
  .admin-table td {
    padding: 12px 16px;
  }

  .member-name {
    min-width: 150px;
  }
}

@media (max-width: 768px) {
  .table-container {
    overflow-x: auto;
  }

  .admin-table {
    min-width: 800px;
  }

  .admin-table th,
  .admin-table td {
    padding: 10px 12px;
  }

  .avatar-small {
    width: 40px;
    height: 40px;
  }

  .member-name strong {
    font-size: 14px;
  }

  .actions {
    gap: 6px;
  }

  .btn-icon {
    width: 32px;
    height: 32px;
    padding: 8px;
  }
}

/* Анимация для строк таблицы */
.admin-table tbody tr {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Улучшенные стили для пустого состояния */
.empty-state {
  padding: 60px 20px;
  text-align: center;
  color: #6b7280;
}

.empty-state i {
  font-size: 48px;
  color: #d1d5db;
  margin-bottom: 16px;
}

.empty-state h3 {
  font-size: 18px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.empty-state p {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
}

/* Стили модального окна */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 20px;
  box-sizing: border-box;
}

.modal-content {
  background: #fff;
  border-radius: 12px;
  padding: 0;
  max-width: 900px;
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  position: relative;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 32px;
  border-bottom: 1px solid #e5e7eb;
  background: #f9fafb;
  border-radius: 12px 12px 0 0;
}

.modal-header h2 {
  font-size: 24px;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.btn-close {
  background: #f3f4f6;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  padding: 8px;
  color: #6b7280;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
}

.btn-close:hover {
  background: #e5e7eb;
  color: #374151;
  transform: scale(1.05);
}

.modal-body {
  padding: 32px;
  max-height: calc(90vh - 140px);
  overflow-y: auto;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 24px;
  margin-bottom: 32px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  font-weight: 600;
  font-size: 14px;
  color: #374151;
  margin: 0;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s ease;
  background: #fff;
  color: #111827;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 100px;
  font-family: inherit;
}

.checkbox-label {
  display: flex;
  align-items: center;
  flex-direction: row !important;
  gap: 12px !important;
  cursor: pointer;
  padding: 16px;
  background: #f9fafb;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  transition: all 0.2s ease;
}

.checkbox-label:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
}

.checkbox-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  margin: 0 !important;
  cursor: pointer;
  accent-color: #3b82f6;
}

.avatar-preview {
  margin-top: 16px;
  text-align: center;
}

.avatar-preview img {
  width: 120px;
  height: 120px;
  border-radius: 12px;
  object-fit: cover;
  border: 3px solid #e5e7eb;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.social-inputs {
  display: flex;
  flex-direction: column;
  gap: 16px;
  background: #f9fafb;
  padding: 20px;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.social-input {
  display: flex;
  align-items: center;
  gap: 12px;
}

.social-input i {
  width: 24px;
  height: 24px;
  text-align: center;
  color: #6b7280;
  font-size: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.social-input input {
  flex: 1;
  margin: 0 !important;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 16px;
  padding: 24px 32px;
  background: #f9fafb;
  border-top: 1px solid #e5e7eb;
  border-radius: 0 0 12px 12px;
  margin: 0 -32px -32px -32px;
}

.btn {
  padding: 12px 24px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
  min-width: 120px;
  justify-content: center;
}

.btn-secondary {
  background-color: #f3f4f6;
  color: #374151;
  border: 2px solid #e5e7eb;
}

.btn-secondary:hover {
  background-color: #e5e7eb;
  border-color: #d1d5db;
  color: #111827;
}

.btn-primary {
  background-color: #3b82f6;
  color: #fff;
  border: 2px solid #3b82f6;
}

.btn-primary:hover:not(:disabled) {
  background-color: #2563eb;
  border-color: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
  box-shadow: none !important;
}

/* Адаптивность модального окна */
@media (max-width: 768px) {
  .modal-overlay {
    padding: 16px;
    align-items: flex-start;
    padding-top: 40px;
  }

  .modal-content {
    max-height: calc(100vh - 80px);
    max-width: 100%;
  }

  .modal-header {
    padding: 20px 24px;
  }

  .modal-header h2 {
    font-size: 20px;
  }

  .modal-body {
    padding: 24px;
  }

  .form-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .modal-actions {
    padding: 20px 24px;
    margin: 0 -24px -24px -24px;
    flex-direction: column-reverse;
  }

  .btn {
    width: 100%;
  }
}

@media (max-width: 480px) {
  .modal-header {
    padding: 16px 20px;
  }

  .modal-body {
    padding: 20px;
  }

  .form-grid {
    gap: 16px;
  }

  .modal-actions {
    padding: 16px 20px;
    margin: 0 -20px -20px -20px;
  }
}
</style>
