<template>
  <div class="admin-users">
    <div class="page-header">
      <div class="header-content">
        <h1><i class="fas fa-users"></i> User Management</h1>
        <p>Manage system users and their permissions</p>
      </div>
      <button @click="openCreateModal" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add New User
      </button>
    </div>

    <!-- Users Table -->
    <div class="content-card">
      <div class="table-header">
        <h3>All Users</h3>
        <div class="table-controls">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search users..."
            class="search-input"
          >
        </div>
      </div>

      <div class="table-container">
        <table class="users-table">
          <thead>
            <tr>
              <th>User</th>
              <th>Email</th>
              <th>Role</th>
              <th>Created</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in filteredUsers" :key="user.id">
              <td>
                <div class="user-cell">
                  <div class="user-avatar">
                    <i class="fas fa-user"></i>
                  </div>
                  <div class="user-info">
                    <div class="user-name">{{ user.name }}</div>
                    <div class="user-id">#{{ user.id }}</div>
                  </div>
                </div>
              </td>
              <td>{{ user.email }}</td>
              <td>
                <span :class="['role-badge', user.role]">
                  <i :class="getRoleIcon(user.role)"></i>
                  {{ user.role }}
                </span>
              </td>
              <td>{{ formatDate(user.created_at) }}</td>
              <td>
                <div class="action-buttons">
                  <button @click="editUser(user)" class="btn btn-sm btn-secondary">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="deleteUser(user)" class="btn btn-sm btn-danger">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="users.last_page > 1" class="pagination">
        <button
          @click="loadUsers(users.current_page - 1)"
          :disabled="users.current_page === 1"
          class="btn btn-secondary"
        >
          <i class="fas fa-chevron-left"></i>
          Previous
        </button>
        <span class="page-info">
          Page {{ users.current_page }} of {{ users.last_page }}
        </span>
        <button
          @click="loadUsers(users.current_page + 1)"
          :disabled="users.current_page === users.last_page"
          class="btn btn-secondary"
        >
          Next
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- User Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h2>
            <i :class="editingUser ? 'fas fa-user-edit' : 'fas fa-user-plus'"></i>
            {{ editingUser ? 'Edit User' : 'Add New User' }}
          </h2>
          <button @click="closeModal" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveUser" class="modal-form">
          <div class="form-row">
            <div class="form-group">
              <label for="name">
                <i class="fas fa-user"></i>
                Full Name
              </label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                placeholder="Enter full name"
              >
            </div>
            <div class="form-group">
              <label for="email">
                <i class="fas fa-envelope"></i>
                Email Address
              </label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                placeholder="user@example.com"
              >
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="password">
                <i class="fas fa-lock"></i>
                Password
              </label>
              <input
                id="password"
                v-model="form.password"
                type="password"
                :required="!editingUser"
                :placeholder="editingUser ? 'Leave blank to keep current' : 'Enter password'"
              >
            </div>
            <div class="form-group">
              <label for="role">
                <i class="fas fa-shield-alt"></i>
                Role
              </label>
              <select id="role" v-model="form.role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
              </select>
            </div>
          </div>

          <div v-if="error" class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            {{ error }}
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              <i class="fas fa-times"></i>
              Cancel
            </button>
            <button type="submit" :disabled="saving" class="btn btn-primary">
              <div v-if="saving" class="loading-spinner"></div>
              <i v-else :class="editingUser ? 'fas fa-save' : 'fas fa-plus'"></i>
              {{ saving ? 'Saving...' : (editingUser ? 'Update User' : 'Create User') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { adminApiService } from '../admin-services/adminApi'
import { useNotifications } from '../composables/useNotifications'

const { showSuccess, showError, showWarning } = useNotifications()

const users = ref({ data: [], current_page: 1, last_page: 1 })
const showModal = ref(false)
const editingUser = ref(null)
const saving = ref(false)
const error = ref('')
const searchQuery = ref('')

const form = ref({
  name: '',
  email: '',
  password: '',
  role: 'user'
})

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value.data

  const query = searchQuery.value.toLowerCase()
  return users.value.data.filter(user =>
    user.name.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query) ||
    user.role.toLowerCase().includes(query)
  )
})

const loadUsers = async (page = 1) => {
  try {
    const response = await adminApiService.getUsers(page)
    users.value = response.data
  } catch (error) {
    console.error('Failed to load users:', error)
    showError('Failed to load users. Please try again.')
  }
}

const openCreateModal = () => {
  editingUser.value = null
  form.value = { name: '', email: '', password: '', role: 'user' }
  error.value = ''
  showModal.value = true
}

const editUser = (user) => {
  editingUser.value = user
  form.value = { ...user, password: '' }
  error.value = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingUser.value = null
  error.value = ''
}

const saveUser = async () => {
  try {
    saving.value = true
    error.value = ''

    if (editingUser.value) {
      await adminApiService.updateUser(editingUser.value.id, form.value)
      showSuccess('User updated successfully!')
    } else {
      await adminApiService.createUser(form.value)
      showSuccess('User created successfully!')
    }

    closeModal()
    loadUsers(users.value.current_page)
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save user'
    showError(err.response?.data?.message || 'Failed to save user')
    console.error('Failed to save user:', err)
  } finally {
    saving.value = false
  }
}

const deleteUser = async (user) => {
  if (confirm(`Are you sure you want to delete "${user.name}"? This action cannot be undone.`)) {
    try {
      await adminApiService.deleteUser(user.id)
      showSuccess(`User "${user.name}" deleted successfully`)
      loadUsers(users.value.current_page)
    } catch (error) {
      console.error('Failed to delete user:', error)
      showError('Failed to delete user. Please try again.')
    }
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getRoleIcon = (role) => {
  return role === 'admin' ? 'fas fa-crown' : 'fas fa-user'
}

onMounted(() => {
  loadUsers()
})
</script>

<style scoped>
/* Page Styles */
.admin-users {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.header-content h1 {
  font-size: 2rem;
  font-weight: 700;
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.header-content p {
  color: #7f8c8d;
  margin: 0;
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
}

.table-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
}

.search-input {
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  width: 250px;
  transition: border-color 0.2s;
}

.search-input:focus {
  outline: none;
  border-color: #3498db;
}

.table-container {
  overflow-x: auto;
}

.users-table {
  width: 100%;
  border-collapse: collapse;
}

.users-table th {
  background: #f8f9fa;
  padding: 1rem 2rem;
  text-align: left;
  font-weight: 600;
  color: #495057;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  border-bottom: 1px solid #e9ecef;
}

.users-table td {
  padding: 1rem 2rem;
  border-bottom: 1px solid #f1f3f4;
  vertical-align: middle;
}

.users-table tbody tr:hover {
  background: #f8f9fa;
}

.user-cell {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-avatar {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea, #764ba2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 0.9rem;
}

.user-info {
  flex: 1;
}

.user-name {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.25rem;
}

.user-id {
  font-size: 0.8rem;
  color: #7f8c8d;
}

.role-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: capitalize;
}

.role-badge.admin {
  background: linear-gradient(135deg, #f093fb, #f5576c);
  color: white;
}

.role-badge.user {
  background: #e3f2fd;
  color: #1976d2;
}

.action-buttons {
  display: flex;
  gap: 0.5rem;
}

/* Button Styles */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.9rem;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea, #764ba2);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
}

.btn-danger {
  background: #dc3545;
  color: white;
}

.btn-danger:hover {
  background: #c82333;
}

.btn-sm {
  padding: 0.5rem;
  font-size: 0.8rem;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none !important;
}

/* Pagination */
.pagination {
  padding: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  border-top: 1px solid #f1f3f4;
}

.page-info {
  color: #6c757d;
  font-size: 0.9rem;
}

/* Modal Styles */
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
  z-index: 2000;
  backdrop-filter: blur(4px);
}

.modal {
  background: white;
  border-radius: 16px;
  padding: 0;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
  animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.modal-header {
  padding: 2rem 2rem 1rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #e9ecef;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
  font-weight: 600;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.close-btn {
  background: #f8f9fa;
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
  color: #6c757d;
}

.close-btn:hover {
  background: #e9ecef;
  color: #495057;
}

.modal-form {
  padding: 2rem;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #2c3e50;
  font-size: 0.9rem;
}

.form-group input,
.form-group select {
  padding: 0.875rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: border-color 0.2s;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.error-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #f8d7da;
  color: #721c24;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  border: 1px solid #f5c6cb;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 1rem;
  border-top: 1px solid #e9ecef;
}

.loading-spinner {
  width: 16px;
  height: 16px;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Mobile Styles */
@media (max-width: 768px) {
  .admin-users {
    padding: 1rem;
  }

  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .table-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .search-input {
    width: 100%;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .modal {
    width: 95%;
    margin: 1rem;
  }

  .modal-form {
    padding: 1.5rem;
  }
}
</style>
