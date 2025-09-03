<template>
  <div class="admin-careers">
    <div class="admin-header">
      <h1>Career Management</h1>
      <button @click="showCreateModal" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add New Position
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ careers.length }}</div>
          <div class="stat-label">Total Positions</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-eye"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ activeCareers }}</div>
          <div class="stat-label">Active Positions</div>
        </div>
      </div>
    </div>

    <!-- Careers Table -->
    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Title</th>
            <th>Department</th>
            <th>Type</th>
            <th>Location</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="career in careers" :key="career.id">
            <td class="career-title">
              <strong>{{ career.title }}</strong>
              <small>{{ career.experience_level }}</small>
            </td>
            <td>{{ career.department }}</td>
            <td>
              <span class="badge" :class="getTypeClass(career.employment_type)">
                {{ career.formatted_employment_type }}
              </span>
            </td>
            <td>
              {{ career.location }}
              <span v-if="career.remote_available" class="remote-badge">Remote OK</span>
            </td>
            <td>
              <span class="status-badge" :class="career.is_active ? 'active' : 'inactive'">
                {{ career.is_active ? 'Active' : 'Inactive' }}
              </span>
            </td>
            <td>{{ career.priority }}</td>
            <td class="actions">
              <button @click="editCareer(career)" class="btn-icon" title="Edit">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="deleteCareer(career)" class="btn-icon danger" title="Delete">
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
          <h2>{{ isEditing ? 'Edit Position' : 'Create New Position' }}</h2>
          <button @click="closeModal" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveCareer" class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label>Title *</label>
              <input v-model="form.title" type="text" required>
            </div>

            <div class="form-group">
              <label>Department *</label>
              <input v-model="form.department" type="text" required>
            </div>

            <div class="form-group">
              <label>Employment Type *</label>
              <select v-model="form.employment_type" required>
                <option value="full-time">Full Time</option>
                <option value="part-time">Part Time</option>
                <option value="contract">Contract</option>
                <option value="internship">Internship</option>
              </select>
            </div>

            <div class="form-group">
              <label>Experience Level *</label>
              <select v-model="form.experience_level" required>
                <option value="junior">Junior</option>
                <option value="mid">Mid</option>
                <option value="senior">Senior</option>
                <option value="lead">Lead</option>
              </select>
            </div>

            <div class="form-group">
              <label>Location *</label>
              <input v-model="form.location" type="text" required>
            </div>

            <div class="form-group">
              <label>Salary Range</label>
              <input v-model="form.salary_range" type="text" placeholder="e.g., $50,000 - $80,000">
            </div>

            <div class="form-group full-width">
              <label>Short Description *</label>
              <textarea v-model="form.short_description" rows="3" required></textarea>
            </div>

            <div class="form-group full-width">
              <label>Full Description *</label>
              <textarea v-model="form.description" rows="6" required></textarea>
            </div>

            <div class="form-group full-width">
              <label>Requirements *</label>
              <textarea v-model="form.requirements" rows="6" required></textarea>
            </div>

            <div class="form-group full-width">
              <label>Benefits</label>
              <textarea v-model="form.benefits" rows="4"></textarea>
            </div>

            <div class="form-group">
              <label>Skills (comma-separated)</label>
              <input v-model="skillsInput" type="text" placeholder="JavaScript, PHP, Laravel">
            </div>

            <div class="form-group">
              <label>Priority</label>
              <input v-model.number="form.priority" type="number" min="0" max="100">
            </div>

            <div class="form-group">
              <label>Application Deadline</label>
              <input v-model="form.application_deadline" type="date">
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input v-model="form.remote_available" type="checkbox">
                Remote Available
              </label>
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input v-model="form.is_active" type="checkbox">
                Active Position
              </label>
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn btn-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary" :disabled="loading">
              <i class="fas fa-save"></i>
              {{ loading ? 'Saving...' : 'Save Position' }}
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
  name: 'AdminCareers',
  setup() {
    const careers = ref([])
    const showModal = ref(false)
    const isEditing = ref(false)
    const loading = ref(false)
    const skillsInput = ref('')

    const form = ref({
      title: '',
      department: '',
      employment_type: 'full-time',
      location: '',
      remote_available: false,
      short_description: '',
      description: '',
      requirements: '',
      benefits: '',
      salary_range: '',
      experience_level: 'mid',
      skills: [],
      is_active: true,
      priority: 0,
      application_deadline: null
    })

    const activeCareers = computed(() =>
      careers.value.filter(career => career.is_active).length
    )

    const getAuthHeaders = () => {
      const token = localStorage.getItem('admin_token')
      return token ? { Authorization: `Bearer ${token}` } : {}
    }

    const loadCareers = async () => {
      try {
        const response = await axios.get('/api/careers', {
          headers: getAuthHeaders()
        })
        careers.value = response.data.data || []
      } catch (error) {
        console.error('Failed to load careers:', error)
      }
    }

    const showCreateModal = () => {
      resetForm()
      isEditing.value = false
      showModal.value = true
    }

    const editCareer = (career) => {
      Object.keys(form.value).forEach(key => {
        form.value[key] = career[key] || form.value[key]
      })
      skillsInput.value = career.skills ? career.skills.join(', ') : ''
      isEditing.value = true
      showModal.value = true
    }

    const saveCareer = async () => {
      try {
        loading.value = true

        // Process skills
        form.value.skills = skillsInput.value
          .split(',')
          .map(skill => skill.trim())
          .filter(skill => skill.length > 0)

        const url = isEditing.value
          ? `/api/admin/careers/${form.value.id}`
          : '/api/admin/careers'

        const method = isEditing.value ? 'put' : 'post'

        await axios[method](url, form.value, {
          headers: getAuthHeaders()
        })

        closeModal()
        loadCareers()
      } catch (error) {
        console.error('Failed to save career:', error)
      } finally {
        loading.value = false
      }
    }

    const deleteCareer = async (career) => {
      if (!confirm(`Are you sure you want to delete "${career.title}"?`)) return

      try {
        await axios.delete(`/api/admin/careers/${career.id}`, {
          headers: getAuthHeaders()
        })
        loadCareers()
      } catch (error) {
        console.error('Failed to delete career:', error)
      }
    }

    const closeModal = () => {
      showModal.value = false
      resetForm()
    }

    const resetForm = () => {
      Object.assign(form.value, {
        title: '',
        department: '',
        employment_type: 'full-time',
        location: '',
        remote_available: false,
        short_description: '',
        description: '',
        requirements: '',
        benefits: '',
        salary_range: '',
        experience_level: 'mid',
        skills: [],
        is_active: true,
        priority: 0,
        application_deadline: null
      })
      skillsInput.value = ''
    }

    const getTypeClass = (type) => {
      const classes = {
        'full-time': 'success',
        'part-time': 'warning',
        'contract': 'info',
        'internship': 'secondary'
      }
      return classes[type] || 'secondary'
    }

    onMounted(loadCareers)

    return {
      careers,
      activeCareers,
      showModal,
      isEditing,
      loading,
      form,
      skillsInput,
      showCreateModal,
      editCareer,
      saveCareer,
      deleteCareer,
      closeModal,
      getTypeClass
    }
  }
}
</script>

<style scoped>
.admin-careers {
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
  cursor: pointer;
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
  padding: 15px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  font-size: 24px;
  margin-bottom: 10px;
}

.stat-number {
  font-size: 18px;
  font-weight: bold;
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
  background: #f1f1f1;
}

.career-title {
  display: flex;
  flex-direction: column;
}

.badge {
  display: inline-block;
  padding: 5px 10px;
  border-radius: 12px;
  font-size: 12px;
  color: #fff;
}

.badge.success {
  background-color: #28a745;
}

.badge.warning {
  background-color: #ffc107;
}

.badge.info {
  background-color: #17a2b8;
}

.badge.secondary {
  background-color: #6c757d;
}

.remote-badge {
  display: inline-block;
  margin-top: 5px;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 12px;
  color: #fff;
  background-color: #007bff;
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
  max-width: 600px;
  width: 100%;
  position: relative;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.modal-header h2 {
  font-size: 18px;
  margin: 0;
}

.btn-close {
  background: none;
  border: none;
  font-size: 18px;
  cursor: pointer;
}

.modal-body {
  max-height: 400px;
  overflow-y: auto;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 5px;
  font-weight: bold;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 14px;
}

.form-group textarea {
  resize: vertical;
}

.checkbox-label {
  display: flex;
  align-items: center;
}

.checkbox-label input {
  margin-right: 10px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 15px;
}

.modal-actions .btn {
  margin-left: 10px;
}
</style>
