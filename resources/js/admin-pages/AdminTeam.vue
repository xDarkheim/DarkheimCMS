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
      <table class="admin-table">
        <thead>
          <tr>
            <th>Photo</th>
            <th>Name</th>
            <th>Position</th>
            <th>Department</th>
            <th>Status</th>
            <th>Visible</th>
            <th>Priority</th>
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
            <td>{{ member.priority }}</td>
            <td class="actions">
              <button @click="editMember(member)" class="btn-icon" title="Edit">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="deleteMember(member)" class="btn-icon danger" title="Delete">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
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
              <label>Phone</label>
              <input v-model="form.phone" type="text">
            </div>

            <div class="form-group">
              <label>Priority</label>
              <input v-model.number="form.priority" type="number" min="0" max="100">
            </div>

            <div class="form-group">
              <label>Joined Date</label>
              <input v-model="form.joined_date" type="date">
            </div>

            <div class="form-group full-width">
              <label>Avatar</label>
              <input @change="handleAvatarChange" type="file" accept="image/*">
              <div v-if="avatarPreview" class="avatar-preview">
                <img :src="avatarPreview" alt="Preview">
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
      phone: '',
      skills: [],
      social_links: {},
      status: 'active',
      joined_date: null,
      priority: 0,
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
      showModal.value = true
    }

    const editMember = (member) => {
      Object.keys(form.value).forEach(key => {
        form.value[key] = member[key] || form.value[key]
      })

      // Важно: добавляем ID для правильного редактирования
      form.value.id = member.id

      skillsInput.value = member.skills ? member.skills.join(', ') : ''

      // Set social links
      if (member.social_links) {
        Object.keys(socialLinks.value).forEach(platform => {
          socialLinks.value[platform] = member.social_links[platform] || ''
        })
      }

      isEditing.value = true
      showModal.value = true
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
          .filter(([, url]) => url.trim() !== '')
          .reduce((acc, [platform, url]) => {
            acc[platform] = url
            return acc
          }, {})

        // Create FormData for file upload
        const formData = new FormData()
        Object.keys(form.value).forEach(key => {
          if (key === 'skills' || key === 'social_links') {
            formData.append(key, JSON.stringify(form.value[key]))
          } else {
            formData.append(key, form.value[key] || '')
          }
        })

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
        alert('Error saving team member: ' + (error.response?.data?.message || error.message))
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
        phone: '',
        skills: [],
        social_links: {},
        status: 'active',
        joined_date: null,
        priority: 0,
        show_on_website: true
      })
      skillsInput.value = ''
      avatarFile.value = null
      avatarPreview.value = null
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
      showCreateModal,
      editMember,
      saveMember,
      deleteMember,
      closeModal,
      handleAvatarChange
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
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.admin-table {
  width: 100%;
  border-collapse: collapse;
}

.admin-table th,
.admin-table td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.admin-table th {
  background: #f7f7f7;
  font-weight: bold;
}

.admin-table tr:hover {
  background: #f9f9f9;
}

.avatar-cell {
  width: 60px;
}

.avatar-small {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.member-name {
  display: flex;
  flex-direction: column;
}

.member-name strong {
  margin-bottom: 2px;
}

.member-name small {
  color: #666;
  font-size: 12px;
}

.status-badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 12px;
  color: #fff;
}

.status-badge.active {
  background-color: #28a745;
}

.status-badge.inactive {
  background-color: #dc3545;
}

.status-badge.on-leave {
  background-color: #ffc107;
}

.visibility-badge {
  display: inline-block;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 12px;
  color: #fff;
}

.visibility-badge.visible {
  background-color: #28a745;
}

.visibility-badge.hidden {
  background-color: #dc3545;
}

.actions {
  display: flex;
  gap: 5px;
}

.btn-icon {
  background: none;
  border: none;
  padding: 8px;
  cursor: pointer;
  border-radius: 4px;
  font-size: 14px;
}

.btn-icon:hover {
  background-color: #f1f1f1;
}

.btn-icon.danger:hover {
  background-color: #f8d7da;
  color: #dc3545;
}

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
  z-index: 1000;
}

.modal-content {
  background: #fff;
  border-radius: 8px;
  padding: 20px;
  max-width: 800px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 1px solid #ddd;
}

.modal-header h2 {
  font-size: 20px;
  margin: 0;
}

.btn-close {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
  padding: 5px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
  margin-bottom: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.form-group label {
  margin-bottom: 5px;
  font-weight: bold;
  font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.form-group textarea {
  resize: vertical;
}

.checkbox-label {
  display: flex;
  align-items: center;
  flex-direction: row;
}

.checkbox-label input {
  margin-right: 10px;
  margin-bottom: 0;
}

.avatar-preview {
  margin-top: 10px;
}

.avatar-preview img {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
}

.social-inputs {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.social-input {
  display: flex;
  align-items: center;
  gap: 10px;
}

.social-input i {
  width: 20px;
  text-align: center;
  color: #666;
}

.social-input input {
  flex: 1;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #ddd;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-secondary {
  background-color: #6c757d;
  color: #fff;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
