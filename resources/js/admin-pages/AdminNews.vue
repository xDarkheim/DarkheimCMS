<template>
  <div class="admin-news">
    <div class="page-header">
      <div class="header-content">
        <h1><i class="fas fa-newspaper"></i> News Management</h1>
        <p>Create and manage news articles and announcements</p>
      </div>
      <button @click="openCreateModal" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add New Article
      </button>
    </div>

    <!-- News List -->
    <div class="content-card">
      <div class="table-header">
        <h3>All Articles</h3>
        <div class="table-controls">
          <select v-model="filterStatus" @change="loadNews(1)" class="filter-select">
            <option value="">All Status</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="featured">Featured</option>
          </select>
        </div>
      </div>

      <div class="news-list">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading news articles...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error && !loading" class="error-state">
          <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <h3>Failed to Load News</h3>
          <p>{{ error }}</p>
          <button @click="loadNews()" class="btn btn-primary">
            <i class="fas fa-redo"></i>
            Retry
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!loading && filteredNews.length === 0" class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-newspaper"></i>
          </div>
          <h3>No Articles Found</h3>
          <p v-if="filterStatus">No articles found with the selected status filter.</p>
          <p v-else>No news articles have been created yet.</p>
          <button @click="openCreateModal" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Create First Article
          </button>
        </div>

        <!-- News Cards -->
        <div v-else v-for="article in filteredNews" :key="article.id" class="news-card">
          <div class="news-image">
            <img v-if="article.image_url" :src="article.image_url" :alt="article.title">
            <div v-else class="no-image">
              <i class="fas fa-newspaper"></i>
            </div>
            <div class="news-overlay">
              <div class="news-actions">
                <button @click="editArticle(article)" class="btn btn-sm btn-light">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="deleteArticle(article)" class="btn btn-sm btn-danger">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="news-content">
            <div class="news-header">
              <h3>{{ article.title }}</h3>
              <div class="news-badges">
                <span v-if="article.is_featured" class="badge featured">
                  <i class="fas fa-star"></i> Featured
                </span>
                <span :class="['badge', article.is_published ? 'published' : 'draft']">
                  {{ article.is_published ? 'Published' : 'Draft' }}
                </span>
              </div>
            </div>

            <p class="news-excerpt">{{ article.excerpt || truncateText(article.content, 150) }}</p>

            <div class="news-meta">
              <div class="meta-item">
                <i class="fas fa-user"></i>
                <span>{{ article.author }}</span>
              </div>
              <div class="meta-item">
                <i class="fas fa-folder"></i>
                <span>{{ getCategoryDisplayName(article.category) }}</span>
              </div>
              <div class="meta-item">
                <i class="fas fa-eye"></i>
                <span>{{ article.views }} views</span>
              </div>
              <div v-if="article.published_at" class="meta-item">
                <i class="fas fa-calendar"></i>
                <span>{{ formatDate(article.published_at) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="news.last_page > 1" class="pagination">
        <button
          @click="loadNews(news.current_page - 1)"
          :disabled="news.current_page === 1"
          class="btn btn-secondary"
        >
          <i class="fas fa-chevron-left"></i>
          Previous
        </button>
        <span class="page-info">
          Page {{ news.current_page }} of {{ news.last_page }}
        </span>
        <button
          @click="loadNews(news.current_page + 1)"
          :disabled="news.current_page === news.last_page"
          class="btn btn-secondary"
        >
          Next
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Article Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal modal-large" @click.stop>
        <div class="modal-header">
          <h2>
            <i :class="editingArticle ? 'fas fa-edit' : 'fas fa-plus'"></i>
            {{ editingArticle ? 'Edit Article' : 'Add New Article' }}
          </h2>
          <button @click="closeModal" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveArticle" class="modal-form">
          <div class="form-row">
            <div class="form-group">
              <label for="title">
                <i class="fas fa-heading"></i>
                Article Title
              </label>
              <input id="title" v-model="form.title" type="text" required>
            </div>
            <div class="form-group">
              <label for="author">
                <i class="fas fa-user"></i>
                Author
              </label>
              <input id="author" v-model="form.author" type="text" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="category">
                <i class="fas fa-folder"></i>
                Category
              </label>
              <select id="category" v-model="form.category" required>
                <option value="">Select a category</option>
                <option
                  v-for="(displayName, key) in categories"
                  :key="key"
                  :value="key"
                >
                  {{ displayName }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label for="image_url">
                <i class="fas fa-image"></i>
                Featured Image URL
              </label>
              <input id="image_url" v-model="form.image_url" type="url">
            </div>
          </div>

          <div class="form-group">
            <label for="excerpt">
              <i class="fas fa-align-left"></i>
              Excerpt
            </label>
            <textarea id="excerpt" v-model="form.excerpt" rows="2" placeholder="Brief summary of the article"></textarea>
          </div>

          <div class="form-group">
            <label for="content">
              <i class="fas fa-file-text"></i>
              Content
            </label>
            <textarea id="content" v-model="form.content" rows="8" required placeholder="Write your article content here..."></textarea>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="published_at">
                <i class="fas fa-calendar"></i>
                Published Date
              </label>
              <input id="published_at" v-model="form.published_at" type="datetime-local">
            </div>
            <div class="form-group">
              <label>
                <i class="fas fa-cog"></i>
                Article Status
              </label>
              <div class="status-controls">
                <label class="checkbox-label">
                  <input v-model="form.is_published" type="checkbox">
                  <span class="checkmark"></span>
                  <span class="checkbox-text">
                    <i class="fas fa-globe"></i>
                    Published
                  </span>
                </label>
                <label class="checkbox-label">
                  <input v-model="form.is_featured" type="checkbox">
                  <span class="checkmark"></span>
                  <span class="checkbox-text">
                    <i class="fas fa-star"></i>
                    Featured
                  </span>
                </label>
              </div>
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
            <button type="button" @click="saveDraft" :disabled="saving" class="btn btn-draft">
              <div v-if="saving" class="loading-spinner"></div>
              <i v-else class="fas fa-file-alt"></i>
              {{ saving ? 'Saving...' : 'Save as Draft' }}
            </button>
            <button type="submit" :disabled="saving" class="btn btn-primary">
              <div v-if="saving" class="loading-spinner"></div>
              <i v-else :class="editingArticle ? 'fas fa-save' : 'fas fa-plus'"></i>
              {{ saving ? 'Saving...' : (editingArticle ? 'Update Article' : 'Create Article') }}
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

const news = ref({ data: [], current_page: 1, last_page: 1 })
const showModal = ref(false)
const editingArticle = ref(null)
const saving = ref(false)
const loading = ref(false)
const error = ref('')
const filterStatus = ref('')

const form = ref({
  title: '',
  content: '',
  excerpt: '',
  image_url: '',
  author: 'Darkheim Team', // Default author
  category: '',
  is_published: false,
  is_featured: false,
  published_at: ''
})

const categories = ref({})

const filteredNews = computed(() => {
  // Убираем клиентскую фильтрацию, так как фильтрация происходит на сервере
  return news.value.data || []
})

const loadNews = async (page = 1) => {
  try {
    loading.value = true
    error.value = ''

    // Проверяем наличие токена перед запросом
    const token = localStorage.getItem('admin_token')
    console.log('Loading news - Token check:', {
      hasToken: !!token,
      tokenLength: token ? token.length : 0,
      page: page
    })

    if (!token) {
      showError('No authentication token found. Please log in again.')
      return
    }

    const params = {}
    if (filterStatus.value) {
      params.status = filterStatus.value
    }

    const response = await adminApiService.getNews(page, params)

    if (response.data.success) {
      news.value = response.data.data
    } else {
      news.value = response.data
    }

    console.log('Loaded news successfully:', {
      articlesCount: news.value.data?.length || 0,
      currentPage: news.value.current_page,
      totalPages: news.value.last_page
    })
  } catch (err) {
    console.error('Failed to load news:', {
      error: err.message,
      status: err.response?.status,
      statusText: err.response?.statusText,
      data: err.response?.data
    })

    // Show user-friendly error based on status
    if (err.response?.status === 401) {
      showError('Authentication required. Please log in again.')
      // Автоматически перенаправляем на страницу входа
      setTimeout(() => {
        window.location.href = '/admin/login'
      }, 2000)
    } else if (err.response?.status === 403) {
      showError('Access denied. You do not have permission to view news.')
    } else if (err.response?.status === 500) {
      showError('Server error. Please try again later.')
    } else if (err.response?.status === 404) {
      showError('News API endpoint not found.')
    } else {
      showError('Failed to load news articles')
    }
  } finally {
    loading.value = false
  }
}

const loadCategories = async () => {
  try {
    const response = await adminApiService.getNewsCategories()
    categories.value = response.data.data || {}
    console.log('Loaded categories:', categories.value)
  } catch (error) {
    console.error('Failed to load categories:', error)
    showWarning('Failed to load categories. Using default categories.')
    categories.value = {}
  }
}

const openCreateModal = () => {
  editingArticle.value = null
  form.value = {
    title: '',
    content: '',
    excerpt: '',
    image_url: '',
    author: 'Darkheim Team',
    category: 'general', // Default to general category
    is_published: false,
    is_featured: false,
    published_at: ''
  }
  error.value = ''
  showModal.value = true
}

const editArticle = (article) => {
  editingArticle.value = article
  form.value = {
    ...article,
    published_at: article.published_at ? new Date(article.published_at).toISOString().slice(0, 16) : ''
  }
  error.value = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingArticle.value = null
  error.value = ''
}

const saveArticle = async () => {
  try {
    saving.value = true
    error.value = ''

    // Prepare form data
    const formData = { ...form.value }

    // Convert empty strings to null for optional fields
    if (!formData.excerpt) formData.excerpt = null
    if (!formData.image_url) formData.image_url = null
    if (!formData.published_at) formData.published_at = null

    let response
    if (editingArticle.value) {
      response = await adminApiService.updateNews(editingArticle.value.id, formData)
      showSuccess('Article updated successfully!')
    } else {
      response = await adminApiService.createNews(formData)
      showSuccess('Article created successfully!')
    }

    console.log('Save response:', response.data)

    closeModal()
    await loadNews(news.value.current_page)

  } catch (err) {
    console.error('Failed to save article:', err)

    if (err.response?.data?.errors) {
      // Validation errors
      const errors = err.response.data.errors
      const errorMessages = Object.values(errors).flat()
      error.value = errorMessages.join(', ')
      showError(`Validation error: ${errorMessages.join(', ')}`)
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
      showError(err.response.data.message)
    } else {
      error.value = 'Failed to save article. Please check your input and try again.'
      showError('Failed to save article. Please check your input and try again.')
    }
  } finally {
    saving.value = false
  }
}

const saveDraft = async () => {
  try {
    saving.value = true
    error.value = ''

    // Prepare form data for draft
    const formData = {
      ...form.value,
      is_published: false,  // Принудительно устанавливаем как неопубликованный
      is_featured: false    // Черновики не могут быть избранными
    }

    // Convert empty strings to null for optional fields
    if (!formData.excerpt) formData.excerpt = null
    if (!formData.image_url) formData.image_url = null
    if (!formData.published_at) formData.published_at = null

    let response
    if (editingArticle.value) {
      response = await adminApiService.updateNews(editingArticle.value.id, formData)
      showSuccess('Article updated as draft successfully!')
    } else {
      response = await adminApiService.createNews(formData)
      showSuccess('Article created as draft successfully!')
    }

    console.log('Save draft response:', response.data)

    closeModal()
    await loadNews(news.value.current_page)

  } catch (err) {
    console.error('Failed to save draft:', err)

    if (err.response?.data?.errors) {
      // Validation errors
      const errors = err.response.data.errors
      const errorMessages = Object.values(errors).flat()
      error.value = errorMessages.join(', ')
      showError(`Validation error: ${errorMessages.join(', ')}`)
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message
      showError(err.response.data.message)
    } else {
      error.value = 'Failed to save draft. Please check your input and try again.'
      showError('Failed to save draft. Please check your input and try again.')
    }
  } finally {
    saving.value = false
  }
}

const deleteArticle = async (article) => {
  if (confirm(`Are you sure you want to delete "${article.title}"? This action cannot be undone.`)) {
    try {
      await adminApiService.deleteNews(article.id)
      showSuccess(`Article "${article.title}" deleted successfully`)
      loadNews(news.value.current_page)
    } catch (error) {
      console.error('Failed to delete article:', error)
      showError('Failed to delete article. Please try again.')
    }
  }
}

const formatDate = (dateString) => {
  if (!dateString) return 'Not set'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const getCategoryDisplayName = (categoryKey) => {
  return categories.value[categoryKey] || categoryKey
}

onMounted(() => {
  console.log('AdminNews component mounted')
  loadNews()
  loadCategories()
})
</script>

<style scoped>
.admin-news {
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

.filter-select {
  padding: 0.75rem 1rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  background: white;
  min-width: 160px;
}

.news-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
  gap: 1.5rem;
  padding: 2rem;
}

.news-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  border: 1px solid #f1f3f4;
}

.news-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.news-image {
  position: relative;
  height: 180px;
  overflow: hidden;
  background: #f8f9fa;
}

.news-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-image {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6c757d;
  font-size: 2rem;
}

.news-overlay {
  position: absolute;
  top: 0;
  right: 0;
  left: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.news-card:hover .news-overlay {
  opacity: 1;
}

.news-actions {
  display: flex;
  gap: 0.5rem;
}

.news-content {
  padding: 1.5rem;
}

.news-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.news-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
  margin: 0;
  flex: 1;
  line-height: 1.4;
}

.news-badges {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  margin-left: 1rem;
}

.badge {
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.7rem;
  font-weight: 500;
  text-align: center;
  white-space: nowrap;
}

.badge.featured {
  background: linear-gradient(135deg, #f093fb, #f5576c);
  color: white;
}

.badge.published {
  background: #d4edda;
  color: #155724;
}

.badge.draft {
  background: #fff3cd;
  color: #856404;
}

.news-excerpt {
  color: #6c757d;
  font-size: 0.9rem;
  margin-bottom: 1rem;
  line-height: 1.6;
}

.news-meta {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  color: #6c757d;
}

.meta-item i {
  width: 12px;
}

/* Button and Form Styles (reusing from previous components) */
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

.btn-light {
  background: white;
  color: #495057;
  border: 1px solid #dee2e6;
}

.btn-danger {
  background: #dc3545;
  color: white;
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

.modal-large {
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
}

.modal {
  background: white;
  border-radius: 16px;
  padding: 0;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
  animation: modalSlideIn 0.3s ease;
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
.form-group textarea,
.form-group select {
  padding: 0.875rem;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  font-size: 0.9rem;
  transition: border-color 0.2s;
  font-family: inherit;
  background: white;
}

.form-group textarea {
  resize: vertical;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group select {
  cursor: pointer;
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

.pagination {
  padding: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  border-top: 1px solid #f1f3f4;
}

/* Loading, Error, and Empty States */
.loading-state,
.error-state,
.empty-state {
  grid-column: 1 / -1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4rem 2rem;
  text-align: center;
}

.loading-state .loading-spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #f3f3f3;
  border-top: 3px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

.error-state .error-icon,
.empty-state .empty-icon {
  font-size: 4rem;
  color: #dee2e6;
  margin-bottom: 1rem;
}

.error-state .error-icon {
  color: #dc3545;
}

.loading-state h3,
.error-state h3,
.empty-state h3 {
  margin-bottom: 1rem;
  color: #6c757d;
  font-size: 1.5rem;
}

.loading-state p,
.error-state p,
.empty-state p {
  color: #6c757d;
  margin-bottom: 2rem;
  max-width: 400px;
}

/* Mobile Styles */
@media (max-width: 768px) {
  .admin-news {
    padding: 1rem;
  }

  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .news-list {
    grid-template-columns: 1fr;
    padding: 1rem;
    gap: 1rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-checkboxes {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>
