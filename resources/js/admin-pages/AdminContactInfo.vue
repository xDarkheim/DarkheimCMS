<template>
  <div class="admin-contact-info">
    <div class="page-header">
      <div class="header-content">
        <h1><i class="fas fa-address-book"></i> Contact Information</h1>
        <p>Manage contact details displayed on your website</p>
      </div>
      <button @click="showAddModal = true" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add Contact Info
      </button>
    </div>

    <!-- Contact Info List -->
    <div class="content-card">
      <div class="table-header">
        <h3>Contact Information Items</h3>
        <div class="table-actions">
          <div class="search-box">
            <i class="fas fa-search"></i>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search contact info..."
            >
          </div>
        </div>
      </div>

      <div v-if="loading" class="loading-state">
        <div class="loading-spinner"></div>
        <p>Loading contact information...</p>
      </div>

      <div v-else-if="filteredContactInfo.length === 0" class="empty-state">
        <div class="empty-icon">
          <i class="fas fa-address-book"></i>
        </div>
        <h3>No Contact Information</h3>
        <p>Start by adding your first contact information item.</p>
        <button @click="showAddModal = true" class="btn btn-primary">
          <i class="fas fa-plus"></i>
          Add Contact Info
        </button>
      </div>

      <div v-else class="contact-info-grid">
        <div
          v-for="info in filteredContactInfo"
          :key="info.id"
          class="contact-info-card"
          :class="{ inactive: !info.is_active }"
        >
          <div class="card-header">
            <div class="info-icon">
              <i :class="info.icon || 'fas fa-info-circle'"></i>
            </div>
            <div class="info-details">
              <h4>{{ info.label }}</h4>
              <span class="info-type">{{ formatType(info.type) }}</span>
            </div>
            <div class="card-actions">
              <button
                @click="toggleActive(info)"
                :class="['btn', 'btn-sm', info.is_active ? 'btn-success' : 'btn-secondary']"
                :title="info.is_active ? 'Active' : 'Inactive'"
              >
                <i :class="info.is_active ? 'fas fa-eye' : 'fas fa-eye-slash'"></i>
              </button>
              <button @click="editContactInfo(info)" class="btn btn-sm btn-primary">
                <i class="fas fa-edit"></i>
              </button>
              <button @click="deleteContactInfo(info)" class="btn btn-sm btn-danger">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
          <div class="card-content">
            <div class="info-value">
              {{ info.value }}
            </div>
            <div class="info-meta">
              <span class="sort-order">Order: {{ info.sort_order }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit Modal -->
    <div v-if="showAddModal || showEditModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>{{ showEditModal ? 'Edit' : 'Add' }} Contact Information</h3>
          <button @click="closeModal" class="modal-close">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveContactInfo" class="modal-body">
          <div class="form-grid">
            <div class="form-group">
              <label for="key">
                Unique Key *
                <span class="field-info">
                  <i class="fas fa-info-circle" title="Unique identifier for this contact item"></i>
                </span>
              </label>
              <input
                id="key"
                v-model="form.key"
                type="text"
                :class="{ 'error': errors.key }"
                placeholder="Auto-generated from type and label"
                required
              >
              <small class="field-help">
                This key is used internally to identify this contact information.
                It will be auto-generated when you fill the label field.
              </small>
              <span v-if="errors.key" class="error-text">{{ errors.key }}</span>
            </div>

            <div class="form-group">
              <label for="label">
                Display Name *
                <span class="field-info">
                  <i class="fas fa-info-circle" title="The name shown to visitors on your website"></i>
                </span>
              </label>
              <input
                id="label"
                v-model="form.label"
                type="text"
                :class="{ 'error': errors.label }"
                placeholder="e.g., Main Phone, Support Email, Office Address"
                required
                @input="generateKey"
              >
              <small class="field-help">
                This is what visitors will see on your website (e.g., "Phone Number", "Email Address")
              </small>
              <span v-if="errors.label" class="error-text">{{ errors.label }}</span>
            </div>

            <div class="form-group">
              <label for="type">
                Contact Type *
                <span class="field-info">
                  <i class="fas fa-info-circle" title="What kind of contact information is this?"></i>
                </span>
              </label>
              <select
                id="type"
                v-model="form.type"
                :class="{ 'error': errors.type }"
                required
                @change="generateKey"
              >
                <option value="">Choose the type of contact information</option>
                <option value="email">📧 Email Address</option>
                <option value="phone">📞 Phone Number</option>
                <option value="address">📍 Physical Address</option>
                <option value="url">🌐 Website URL</option>
                <option value="social">📱 Social Media</option>
                <option value="text">📝 Text Information</option>
              </select>
              <small class="field-help">
                Select the category that best describes this contact information
              </small>
              <span v-if="errors.type" class="error-text">{{ errors.type }}</span>
            </div>

            <div class="form-group">
              <label for="icon">Icon</label>
              <div class="icon-selector">
                <select
                  id="icon"
                  v-model="form.icon"
                  class="icon-select"
                >
                  <option value="">Select an icon</option>
                  <optgroup label="Contact">
                    <option value="fas fa-envelope">📧 Email</option>
                    <option value="fas fa-phone">📞 Phone</option>
                    <option value="fas fa-map-marker-alt">📍 Address</option>
                    <option value="fas fa-globe">🌐 Website</option>
                    <option value="fas fa-fax">📠 Fax</option>
                  </optgroup>
                  <optgroup label="Social Media">
                    <option value="fab fa-facebook">📘 Facebook</option>
                    <option value="fab fa-twitter">🐦 Twitter/X</option>
                    <option value="fab fa-instagram">📷 Instagram</option>
                    <option value="fab fa-linkedin">💼 LinkedIn</option>
                    <option value="fab fa-youtube">📹 YouTube</option>
                    <option value="fab fa-discord">🎮 Discord</option>
                    <option value="fab fa-telegram">✈️ Telegram</option>
                    <option value="fab fa-whatsapp">💬 WhatsApp</option>
                    <option value="fab fa-tiktok">🎵 TikTok</option>
                    <option value="fab fa-github">💻 GitHub</option>
                  </optgroup>
                  <optgroup label="Other">
                    <option value="fas fa-info-circle">ℹ️ Information</option>
                    <option value="fas fa-clock">⏰ Hours</option>
                    <option value="fas fa-calendar">📅 Schedule</option>
                    <option value="fas fa-question-circle">❓ Support</option>
                  </optgroup>
                </select>
                <div class="icon-preview">
                  <i v-if="form.icon" :class="form.icon"></i>
                </div>
              </div>
              <small class="icon-help">Choose a predefined icon or enter custom CSS classes</small>
              <input
                v-model="form.icon"
                type="text"
                class="icon-input"
                placeholder="Or enter custom icon classes (e.g., fas fa-custom)"
              >
            </div>

            <div class="form-group full-width">
              <label for="value">Value *</label>
              <input
                id="value"
                v-model="form.value"
                type="text"
                :class="{ 'error': errors.value }"
                :placeholder="getValuePlaceholder()"
                required
              >
              <span v-if="errors.value" class="error-text">{{ errors.value }}</span>
            </div>

            <div class="form-group">
              <label for="sort_order">Sort Order</label>
              <input
                id="sort_order"
                v-model.number="form.sort_order"
                type="number"
                min="0"
                placeholder="0"
              >
            </div>

            <div class="form-group">
              <label class="checkbox-label">
                <input
                  type="checkbox"
                  v-model="form.is_active"
                >
                <span class="checkmark"></span>
                Active
              </label>
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              Cancel
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="btn btn-primary"
            >
              <span v-if="saving" class="loading-spinner"></span>
              <i v-else class="fas fa-save"></i>
              {{ saving ? 'Saving...' : 'Save' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Toast Notifications -->
    <transition name="toast">
      <div v-if="message" :class="['toast-notification', messageType]">
        <div class="toast-content">
          <i :class="messageType === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'"></i>
          <span>{{ message }}</span>
        </div>
        <button @click="message = ''" class="toast-close">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </transition>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import adminApi from '../admin-services/adminApi.js'

export default {
  name: 'AdminContactInfo',
  setup() {
    const loading = ref(false)
    const saving = ref(false)
    const contactInfoList = ref([])
    const searchQuery = ref('')
    const showAddModal = ref(false)
    const showEditModal = ref(false)
    const message = ref('')
    const messageType = ref('success')

    const form = reactive({
      id: null,
      key: '',
      label: '',
      value: '',
      type: '',
      icon: '',
      is_active: true,
      sort_order: 0
    })

    const errors = reactive({
      key: '',
      label: '',
      value: '',
      type: ''
    })

    const filteredContactInfo = computed(() => {
      if (!searchQuery.value) return contactInfoList.value

      const query = searchQuery.value.toLowerCase()
      return contactInfoList.value.filter(info =>
        info.label.toLowerCase().includes(query) ||
        info.value.toLowerCase().includes(query) ||
        info.type.toLowerCase().includes(query)
      )
    })

    const loadContactInfo = async () => {
      loading.value = true
      try {
        const response = await adminApi.get('/admin/company-info')
        if (response.data.success) {
          contactInfoList.value = response.data.data || []
        }
      } catch (error) {
        console.error('Error loading contact info:', error)
        showMessage('Failed to load contact information', 'error')
      } finally {
        loading.value = false
      }
    }

    const saveContactInfo = async () => {
      clearErrors()

      if (!validateForm()) return

      saving.value = true

      try {
        const endpoint = showEditModal.value
          ? `/admin/company-info/${form.id}`
          : '/admin/company-info'

        const method = showEditModal.value ? 'put' : 'post'

        const response = await adminApi[method](endpoint, {
          key: form.key,
          label: form.label,
          value: form.value,
          type: form.type,
          icon: form.icon,
          is_active: form.is_active,
          sort_order: form.sort_order
        })

        if (response.data.success) {
          showMessage(response.data.message || 'Contact information saved successfully!', 'success')
          closeModal()
          await loadContactInfo()
        }
      } catch (error) {
        console.error('Error saving contact info:', error)

        if (error.response?.data?.errors) {
          Object.assign(errors, error.response.data.errors)
        } else {
          showMessage(error.response?.data?.message || 'Failed to save contact information', 'error')
        }
      } finally {
        saving.value = false
      }
    }

    const editContactInfo = (info) => {
      Object.assign(form, {
        id: info.id,
        key: info.key,
        label: info.label,
        value: info.value,
        type: info.type,
        icon: info.icon || '',
        is_active: info.is_active,
        sort_order: info.sort_order
      })
      showEditModal.value = true
    }

    const deleteContactInfo = async (info) => {
      if (!confirm(`Are you sure you want to delete "${info.label}"?`)) return

      try {
        const response = await adminApi.delete(`/admin/company-info/${info.id}`)

        if (response.data.success) {
          showMessage('Contact information deleted successfully!', 'success')
          await loadContactInfo()
        }
      } catch (error) {
        console.error('Error deleting contact info:', error)
        showMessage('Failed to delete contact information', 'error')
      }
    }

    const toggleActive = async (info) => {
      try {
        const response = await adminApi.put(`/admin/company-info/${info.id}`, {
          ...info,
          is_active: !info.is_active
        })

        if (response.data.success) {
          info.is_active = !info.is_active
          showMessage(`Contact information ${info.is_active ? 'activated' : 'deactivated'}`, 'success')
        }
      } catch (error) {
        console.error('Error toggling contact info status:', error)
        showMessage('Failed to update contact information status', 'error')
      }
    }

    const closeModal = () => {
      showAddModal.value = false
      showEditModal.value = false
      resetForm()
      clearErrors()
    }

    const resetForm = () => {
      Object.assign(form, {
        id: null,
        key: '',
        label: '',
        value: '',
        type: '',
        icon: '',
        is_active: true,
        sort_order: 0
      })
    }

    const clearErrors = () => {
      Object.keys(errors).forEach(key => {
        errors[key] = ''
      })
    }

    const validateForm = () => {
      let isValid = true

      if (!form.key.trim()) {
        errors.key = 'Key is required'
        isValid = false
      }

      if (!form.label.trim()) {
        errors.label = 'Label is required'
        isValid = false
      }

      if (!form.value.trim()) {
        errors.value = 'Value is required'
        isValid = false
      }

      if (!form.type) {
        errors.type = 'Type is required'
        isValid = false
      }

      return isValid
    }

    const formatType = (type) => {
      const types = {
        email: 'Email',
        phone: 'Phone',
        address: 'Address',
        url: 'Website',
        social: 'Social Media',
        text: 'Text'
      }
      return types[type] || type
    }

    const getValuePlaceholder = () => {
      switch (form.type) {
        case 'email':
          return 'example@domain.com'
        case 'phone':
          return '+1 (555) 123-4567'
        case 'address':
          return '123 Main St, City, State 12345'
        case 'url':
          return 'https://www.example.com'
        case 'social':
          return 'https://facebook.com/yourpage'
        default:
          return 'Enter value...'
      }
    }

    const showMessage = (text, type = 'success') => {
      message.value = text
      messageType.value = type
      setTimeout(() => {
        message.value = ''
      }, 4000)
    }

    const generateKey = () => {
      if (form.label && form.type && !showEditModal.value) {
        // Генерируем ключ только для новых записей, не при редактировании
        const sanitizedLabel = form.label.toLowerCase()
          .replace(/[^a-z0-9\s]/g, '') // Убираем специальные символы
          .trim()
          .replace(/\s+/g, '_') // Заменяем пробелы на подчеркивания

        const keyBase = `${form.type}_${sanitizedLabel}`
        form.key = keyBase
      }
    }

    onMounted(() => {
      loadContactInfo()
    })

    return {
      loading,
      saving,
      contactInfoList,
      filteredContactInfo,
      searchQuery,
      showAddModal,
      showEditModal,
      form,
      errors,
      message,
      messageType,
      saveContactInfo,
      editContactInfo,
      deleteContactInfo,
      toggleActive,
      closeModal,
      formatType,
      getValuePlaceholder,
      generateKey
    }
  }
}
</script>

<style lang="scss" scoped>
// Admin Contact Info Styles
.admin-contact-info {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
  background: #f8fafc;
  min-height: 100vh;

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

  // Content Card
  .content-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    overflow: hidden;
  }

  .table-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #f1f3f4;
    display: flex;
    justify-content: space-between;
    align-items: center;

    h3 {
      font-size: 1.25rem;
      font-weight: 600;
      color: #2c3e50;
      margin: 0;
    }

    .table-actions {
      display: flex;
      gap: 1rem;
      align-items: center;

      .search-box {
        position: relative;
        display: flex;
        align-items: center;

        i {
          position: absolute;
          left: 1rem;
          color: #7f8c8d;
          z-index: 1;
        }

        input {
          padding: 0.75rem 1rem 0.75rem 2.5rem;
          border: 1px solid #dee2e6;
          border-radius: 8px;
          font-size: 0.9rem;
          min-width: 250px;
          transition: all 0.2s ease;

          &:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
          }

          &::placeholder {
            color: #adb5bd;
          }
        }
      }
    }
  }

  // Contact Info Grid
  .contact-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    padding: 2rem;
  }

  .contact-info-card {
    background: #f8f9fa;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s ease;

    &:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      border-color: #3498db;
    }

    &.inactive {
      opacity: 0.6;
      background: #f1f3f4;
    }

    .card-header {
      background: white;
      padding: 1.5rem;
      border-bottom: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      gap: 1rem;

      .info-icon {
        width: 48px;
        height: 48px;
        background: #3498db;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
        flex-shrink: 0;
      }

      .info-details {
        flex: 1;

        h4 {
          font-size: 1.1rem;
          font-weight: 600;
          color: #2c3e50;
          margin: 0 0 0.25rem;
        }

        .info-type {
          font-size: 0.85rem;
          color: #7f8c8d;
          background: #f8f9fa;
          padding: 0.25rem 0.5rem;
          border-radius: 4px;
        }
      }

      .card-actions {
        display: flex;
        gap: 0.5rem;
        flex-shrink: 0;
      }
    }

    .card-content {
      padding: 1.5rem;

      .info-value {
        font-size: 0.95rem;
        color: #495057;
        margin-bottom: 1rem;
        word-break: break-all;
      }

      .info-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: #7f8c8d;

        .sort-order {
          background: #e9ecef;
          padding: 0.25rem 0.5rem;
          border-radius: 4px;
        }
      }
    }
  }

  // Loading & Empty States
  .loading-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;

    .loading-spinner {
      width: 48px;
      height: 48px;
      border: 4px solid #f1f3f4;
      border-radius: 50%;
      border-top-color: #3498db;
      animation: spin 1s ease-in-out infinite;
      margin-bottom: 1rem;
    }

    p {
      color: #7f8c8d;
      margin: 0;
    }
  }

  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 4rem 2rem;
    text-align: center;

    .empty-icon {
      width: 80px;
      height: 80px;
      background: #f8f9fa;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 1.5rem;

      i {
        font-size: 2.5rem;
        color: #7f8c8d;
      }
    }

    h3 {
      font-size: 1.5rem;
      font-weight: 600;
      color: #2c3e50;
      margin: 0 0 0.5rem;
    }

    p {
      color: #7f8c8d;
      margin: 0 0 2rem;
      max-width: 400px;
    }
  }

  // Modal Styles
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
    padding: 1rem;
  }

  .modal-content {
    background: white;
    border-radius: 12px;
    width: 100%;
    max-width: 600px;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  }

  .modal-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #f1f3f4;
    display: flex;
    justify-content: space-between;
    align-items: center;

    h3 {
      font-size: 1.25rem;
      font-weight: 600;
      color: #2c3e50;
      margin: 0;
    }

    .modal-close {
      background: none;
      border: none;
      color: #7f8c8d;
      cursor: pointer;
      padding: 0.5rem;
      border-radius: 6px;
      transition: all 0.2s ease;

      &:hover {
        background: #f8f9fa;
        color: #495057;
      }

      i {
        font-size: 1.1rem;
      }
    }
  }

  .modal-body {
    padding: 2rem;
    max-height: 70vh;
    overflow-y: auto;

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1.5rem;

      .form-group {
        &.full-width {
          grid-column: 1 / -1;
        }

        label {
          display: block;
          font-weight: 600;
          color: #374151;
          margin-bottom: 0.5rem;
          font-size: 0.9rem;
          display: flex;
          align-items: center;
          gap: 0.5rem;

          .field-info {
            i {
              color: #7f8c8d;
              cursor: help;
              transition: color 0.2s ease;

              &:hover {
                color: #3498db;
              }
            }
          }
        }

        input,
        select,
        textarea {
          width: 100%;
          padding: 0.75rem 1rem;
          border: 1px solid #dee2e6;
          border-radius: 8px;
          font-size: 0.9rem;
          transition: all 0.2s ease;
          background: #f8fafc;
          color: #495057;

          &:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
          }

          &.error {
            border-color: #e74c3c;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
          }

          &::placeholder {
            color: #adb5bd;
          }
        }

        select {
          cursor: pointer;
          background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
          background-position: right 1rem center;
          background-repeat: no-repeat;
          background-size: 1rem;
          appearance: none;
        }

        .error-text {
          display: block;
          color: #e74c3c;
          font-size: 0.8rem;
          margin-top: 0.25rem;
        }

        .checkbox-label {
          display: flex;
          align-items: center;
          gap: 0.75rem;
          cursor: pointer;
          font-weight: 500;

          input[type="checkbox"] {
            width: auto;
            margin: 0;
          }

          .checkmark {
            position: relative;
            display: inline-block;
            width: 20px;
            height: 20px;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 4px;
            transition: all 0.2s ease;

            &::after {
              content: '';
              position: absolute;
              left: 6px;
              top: 2px;
              width: 6px;
              height: 10px;
              border: solid white;
              border-width: 0 2px 2px 0;
              transform: rotate(45deg);
              opacity: 0;
              transition: opacity 0.2s ease;
            }
          }

          input[type="checkbox"]:checked + .checkmark {
            background: #3498db;
            border-color: #3498db;

            &::after {
              opacity: 1;
            }
          }
        }

        .field-help {
          display: block;
          color: #7f8c8d;
          font-size: 0.8rem;
          margin-top: 0.5rem;
          line-height: 1.4;
          font-style: italic;
        }
      }
    }
  }

  .modal-actions {
    padding: 1.5rem 2rem;
    border-top: 1px solid #f1f3f4;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
  }

  // Buttons
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

    &.btn-sm {
      padding: 0.5rem 0.75rem;
      font-size: 0.8rem;
    }

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

    &.btn-success {
      background: #27ae60;
      color: white;

      &:hover {
        background: #219a52;
      }
    }

    &.btn-danger {
      background: #e74c3c;
      color: white;

      &:hover {
        background: #c0392b;
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

  // Animations
  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }

  // Responsive Design
  @media (max-width: 768px) {
    padding: 1rem;

    .page-header {
      flex-direction: column;
      gap: 1rem;

      .header-content h1 {
        font-size: 1.75rem;
      }
    }

    .table-header {
      flex-direction: column;
      gap: 1rem;
      align-items: stretch;

      .table-actions .search-box input {
        min-width: 100%;
      }
    }

    .contact-info-grid {
      grid-template-columns: 1fr;
      padding: 1.5rem;
    }

    .modal-content {
      margin: 1rem;
      max-width: none;
    }

    .modal-body .form-grid {
      grid-template-columns: 1fr;
    }

    .modal-actions {
      flex-direction: column;

      .btn {
        justify-content: center;
      }
    }

    .toast-notification {
      left: 1rem;
      right: 1rem;
      top: 1rem;
      max-width: none;
    }
  }

  @media (max-width: 480px) {
    .contact-info-card .card-header {
      flex-direction: column;
      align-items: flex-start;
      gap: 1rem;

      .card-actions {
        align-self: flex-end;
      }
    }
  }

  // Icon Selector Styles
  .icon-selector {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;

    .icon-select {
      display: flex;
      align-items: center;
      position: relative;
    }

    .icon-preview {
      position: absolute;
      right: 2.5rem;
      top: 50%;
      transform: translateY(-50%);
      width: 24px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f8f9fa;
      border-radius: 4px;
      z-index: 1;

      i {
        font-size: 1rem;
        color: #3498db;
      }
    }

    .icon-help {
      color: #7f8c8d;
      font-size: 0.8rem;
      margin: 0;
    }

    .icon-input {
      margin-top: 0.5rem;
      font-family: 'Courier New', monospace;
      font-size: 0.85rem;
      background: #f8f9fa;
      border: 1px dashed #dee2e6 !important;

      &:focus {
        background: white;
        border-color: #3498db !important;
      }
    }
  }

  // Optgroup styling
  optgroup {
    font-weight: 600;
    color: #2c3e50;
    background: #f8f9fa;

    option {
      font-weight: 400;
      color: #495057;
      padding-left: 1rem;
    }
  }
}
</style>
