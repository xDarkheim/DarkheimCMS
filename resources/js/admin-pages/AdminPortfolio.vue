<template>
  <div class="admin-portfolio">
    <div class="page-header">
      <div class="header-content">
        <h1><i class="fas fa-briefcase"></i> Portfolio Management</h1>
        <p>Manage your portfolio projects and showcase your work</p>
      </div>
      <button @click="openCreateModal" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add New Project
      </button>
    </div>

    <!-- Portfolio Grid -->
    <div class="content-card">
      <div class="table-header">
        <h3>All Projects</h3>
        <div class="table-controls">
          <select v-model="filterCategory" class="filter-select">
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.name">
              {{ category.name }}
            </option>
          </select>
        </div>
      </div>

      <div class="portfolio-grid">
        <div v-for="project in filteredProjects" :key="project.id" class="portfolio-card">
          <div class="project-image">
            <img v-if="project.image_url" :src="project.image_url" :alt="project.title">
            <div v-else class="no-image">
              <i class="fas fa-image"></i>
            </div>
            <div class="project-overlay">
              <div class="project-actions">
                <button @click="editProject(project)" class="btn btn-sm btn-primary">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="deleteProject(project, $event)" class="btn btn-sm btn-danger">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="project-content">
            <div class="project-header">
              <h3>{{ project.title }}</h3>
              <div class="project-badges">
                <span v-if="project.is_featured" class="badge featured">
                  <i class="fas fa-star"></i> Featured
                </span>
                <span :class="['badge', project.is_published ? 'published' : 'draft']">
                  {{ project.is_published ? 'Published' : 'Draft' }}
                </span>
              </div>
            </div>

            <p class="project-description">{{ project.short_description || 'No description' }}</p>

            <div class="project-meta">
              <div class="meta-item">
                <i class="fas fa-folder"></i>
                <span>{{ project.portfolio_category?.name || project.category || 'Uncategorized' }}</span>
              </div>
              <div v-if="project.client" class="meta-item">
                <i class="fas fa-user"></i>
                <span>{{ project.client }}</span>
              </div>
              <div v-if="project.completed_at" class="meta-item">
                <i class="fas fa-calendar"></i>
                <span>{{ formatDate(project.completed_at) }}</span>
              </div>
            </div>

            <div v-if="project.technologies && project.technologies.length" class="project-tags">
              <span v-for="tech in project.technologies.slice(0, 3)" :key="tech" class="tech-tag">
                {{ tech }}
              </span>
              <span v-if="project.technologies.length > 3" class="tech-more">
                +{{ project.technologies.length - 3 }} more
              </span>
            </div>

            <div class="project-links">
              <a v-if="project.project_url" :href="project.project_url" target="_blank" class="link-btn">
                <i class="fas fa-external-link-alt"></i>
              </a>
              <a v-if="project.github_url" :href="project.github_url" target="_blank" class="link-btn">
                <i class="fab fa-github"></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredProjects.length === 0" class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-folder-open"></i>
          </div>
          <h3>No Projects Found</h3>
          <p>{{ filterCategory ? 'No projects found in this category.' : 'Start by creating your first portfolio project.' }}</p>
          <button @click="openCreateModal" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            Create Your First Project
          </button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="portfolios.last_page > 1" class="pagination">
        <button
          @click="loadProjects(portfolios.current_page - 1)"
          :disabled="portfolios.current_page === 1"
          class="btn btn-secondary"
        >
          <i class="fas fa-chevron-left"></i>
          Previous
        </button>
        <span class="page-info">
          Page {{ portfolios.current_page }} of {{ portfolios.last_page }}
        </span>
        <button
          @click="loadProjects(portfolios.current_page + 1)"
          :disabled="portfolios.current_page === portfolios.last_page"
          class="btn btn-secondary"
        >
          Next
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Project Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal modal-large" @click.stop>
        <div class="modal-header">
          <h2>
            <i :class="editingProject ? 'fas fa-edit' : 'fas fa-plus'"></i>
            {{ editingProject ? 'Edit Project' : 'Add New Project' }}
          </h2>
          <button @click="closeModal" class="close-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveProject" class="modal-form">
          <!-- Basic Information -->
          <div class="form-section">
            <h3><i class="fas fa-info-circle"></i> Basic Information</h3>

            <div class="form-row">
              <div class="form-group">
                <label for="title">
                  <i class="fas fa-heading"></i>
                  Project Title *
                </label>
                <input id="title" v-model="form.title" type="text" required placeholder="Enter project title">
              </div>
              <div class="form-group">
                <label for="portfolio_category_id">
                  <i class="fas fa-folder"></i>
                  Category *
                </label>
                <select
                  id="portfolio_category_id"
                  v-model="form.portfolio_category_id"
                  required
                  class="category-select"
                >
                  <option value="">Select a category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
                <small>Choose from predefined categories</small>
              </div>
            </div>

            <div class="form-group">
              <label for="short_description">
                <i class="fas fa-align-left"></i>
                Short Description
              </label>
              <textarea
                id="short_description"
                v-model="form.short_description"
                rows="2"
                placeholder="Brief description for cards and previews"
              ></textarea>
            </div>

            <div class="form-group">
              <label for="description">
                <i class="fas fa-file-text"></i>
                Full Description *
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="4"
                required
                placeholder="Detailed project description"
              ></textarea>
            </div>
          </div>

          <!-- Images & Media -->
          <div class="form-section">
            <h3><i class="fas fa-images"></i> Images & Media</h3>

            <div class="form-group">
              <label for="image_url">
                <i class="fas fa-image"></i>
                Main Project Image
              </label>
              <input
                id="image_url"
                v-model="form.image_url"
                type="url"
                placeholder="https://example.com/image.jpg"
              >
              <small>Main image displayed in portfolio grid</small>
            </div>

            <div class="form-group">
              <label for="gallery_images">
                <i class="fas fa-images"></i>
                Gallery Images
              </label>
              <div class="gallery-input">
                <div v-for="(image, index) in form.gallery_images" :key="index" class="gallery-item">
                  <input
                    v-model="form.gallery_images[index]"
                    type="url"
                    placeholder="https://example.com/gallery-image.jpg"
                  >
                  <button type="button" @click="removeGalleryImage(index)" class="remove-btn">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
                <button type="button" @click="addGalleryImage" class="add-btn">
                  <i class="fas fa-plus"></i> Add Gallery Image
                </button>
              </div>
              <small>Additional images for project gallery</small>
            </div>
          </div>

          <!-- Project Links -->
          <div class="form-section">
            <h3><i class="fas fa-link"></i> Project Links</h3>

            <div class="form-row">
              <div class="form-group">
                <label for="project_url">
                  <i class="fas fa-external-link-alt"></i>
                  Live Demo URL
                </label>
                <input
                  id="project_url"
                  v-model="form.project_url"
                  type="url"
                  placeholder="https://project-demo.com"
                >
              </div>
              <div class="form-group">
                <label for="github_url">
                  <i class="fab fa-github"></i>
                  GitHub Repository
                </label>
                <input
                  id="github_url"
                  v-model="form.github_url"
                  type="url"
                  placeholder="https://github.com/username/repo"
                >
              </div>
            </div>
          </div>

          <!-- Technical Details -->
          <div class="form-section">
            <h3><i class="fas fa-code"></i> Technical Details</h3>

            <div class="form-group">
              <label for="technologies">
                <i class="fas fa-code"></i>
                Technologies Used
              </label>
              <input
                id="technologies"
                v-model="technologiesString"
                type="text"
                placeholder="React, Laravel, MySQL, Docker, AWS"
              >
              <small>Separate technologies with commas</small>
            </div>
          </div>

          <!-- Client & Timeline -->
          <div class="form-section">
            <h3><i class="fas fa-business-time"></i> Project Details</h3>

            <div class="form-row">
              <div class="form-group">
                <label for="client">
                  <i class="fas fa-user-tie"></i>
                  Client Name
                </label>
                <input
                  id="client"
                  v-model="form.client"
                  type="text"
                  placeholder="Company Name or Client"
                >
              </div>
              <div class="form-group">
                <label for="completed_at">
                  <i class="fas fa-calendar-check"></i>
                  Completion Date
                </label>
                <input
                  id="completed_at"
                  v-model="form.completed_at"
                  type="date"
                >
              </div>
            </div>

            <div class="form-group">
              <label for="sort_order">
                <i class="fas fa-sort"></i>
                Display Order
              </label>
              <input
                id="sort_order"
                v-model.number="form.sort_order"
                type="number"
                placeholder="0"
                min="0"
              >
              <small>Lower numbers appear first</small>
            </div>
          </div>

          <!-- Status & Visibility -->
          <div class="form-section">
            <h3><i class="fas fa-eye"></i> Status & Visibility</h3>

            <div class="form-checkboxes">
              <label class="checkbox-label">
                <input v-model="form.is_published" type="checkbox">
                <span class="checkmark"></span>
                <div class="checkbox-content">
                  <strong>Published</strong>
                  <small>Show this project on the public portfolio</small>
                </div>
              </label>

              <label class="checkbox-label">
                <input v-model="form.is_featured" type="checkbox">
                <span class="checkmark"></span>
                <div class="checkbox-content">
                  <strong>Featured Project</strong>
                  <small>Highlight this project on the homepage</small>
                </div>
              </label>
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
              <i v-else :class="editingProject ? 'fas fa-save' : 'fas fa-plus'"></i>
              {{ saving ? 'Saving...' : (editingProject ? 'Update Project' : 'Create Project') }}
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

const portfolios = ref({ data: [], current_page: 1, last_page: 1 })
const categories = ref([])
const showModal = ref(false)
const editingProject = ref(null)
const saving = ref(false)
const error = ref('')
const filterCategory = ref('')
const isAuthenticated = ref(true)

const form = ref({
  title: '',
  description: '',
  short_description: '',
  image_url: '',
  gallery_images: [],
  project_url: '',
  github_url: '',
  technologies: [],
  portfolio_category_id: '',
  client: '',
  completed_at: '',
  is_featured: false,
  is_published: false,
  sort_order: 0
})

const technologiesString = computed({
  get: () => form.value.technologies?.join(', ') || '',
  set: (value) => {
    form.value.technologies = value ? value.split(',').map(tech => tech.trim()).filter(tech => tech) : []
  }
})

const filteredProjects = computed(() => {
  if (!filterCategory.value) return portfolios.value.data
  return portfolios.value.data.filter(project => {
    const categoryName = project.portfolio_category?.name || project.category || 'Uncategorized'
    return categoryName === filterCategory.value
  })
})

const loadProjects = async (page = 1) => {
  try {
    // Validate authentication before making the request
    const user = await adminApiService.validateToken()
    if (!user) {
      isAuthenticated.value = false
      showError('Authentication required. Please log in again.')
      return
    }

    const response = await adminApiService.getPortfolios(page)
    portfolios.value = response.data

    // Load categories from dedicated API endpoint instead of extracting from projects
    await loadCategories()
  } catch (error) {
    console.error('Failed to load projects:', error)
    if (error.response?.status === 401) {
      isAuthenticated.value = false
      showError('Authentication required. Please log in again.')
    } else {
      showError('Failed to load portfolio projects. Please try again.')
    }
  }
}

const loadCategories = async () => {
  try {
    // Используем существующий API endpoint для получения категорий портфолио
    const response = await adminApiService.getPortfolioCategories()
    if (response.data && Array.isArray(response.data)) {
      categories.value = response.data.map(cat => ({
        id: cat.id,
        name: cat.name,
        slug: cat.slug || cat.name.toLowerCase().replace(/\s+/g, '-'),
        icon: cat.icon || '',
        color: cat.color || ''
      }))
    } else {
      console.warn('Portfolio categories response format unexpected:', response.data)
      // Fallback: extract unique categories from existing projects
      const uniqueCategories = [...new Set(portfolios.value.data?.map(p => p.portfolio_category?.name || p.category).filter(Boolean))]
      categories.value = uniqueCategories.map((name, index) => ({
        id: index + 1,
        name,
        slug: name.toLowerCase().replace(/\s+/g, '-')
      }))
    }
  } catch (error) {
    console.error('Failed to load categories:', error)
    showWarning('Failed to load categories. Using fallback categories.')
    // Fallback: extract unique categories from existing projects
    const uniqueCategories = [...new Set(portfolios.value.data?.map(p => p.portfolio_category?.name || p.category).filter(Boolean))]
    categories.value = uniqueCategories.map((name, index) => ({
      id: index + 1,
      name,
      slug: name.toLowerCase().replace(/\s+/g, '-')
    }))
  }
}

const openCreateModal = () => {
  editingProject.value = null
  form.value = {
    title: '',
    description: '',
    short_description: '',
    image_url: '',
    gallery_images: [],
    project_url: '',
    github_url: '',
    technologies: [],
    portfolio_category_id: '',
    client: '',
    completed_at: '',
    is_featured: false,
    is_published: false,
    sort_order: 0
  }
  error.value = ''
  showModal.value = true
}

const editProject = (project) => {
  editingProject.value = project

  // Добавляем отладочную информацию для диагностики проблемы
  console.log('Editing project:', project)
  console.log('Project category data:', {
    portfolio_category: project.portfolio_category,
    portfolio_category_id: project.portfolio_category_id,
    category: project.category
  })
  console.log('Available categories:', categories.value)

  // Определяем правильный ID категории
  let categoryId = ''
  if (project.portfolio_category && project.portfolio_category.id) {
    categoryId = project.portfolio_category.id
  } else if (project.portfolio_category_id) {
    categoryId = project.portfolio_category_id
  } else if (project.category) {
    // Ищем категорию по имени в списке доступных категорий
    const foundCategory = categories.value.find(cat => cat.name === project.category)
    if (foundCategory) {
      categoryId = foundCategory.id
    }
  }

  console.log('Selected category ID:', categoryId)

  // Правильная обработка даты - преобразование в формат YYYY-MM-DD для input[type="date"]
  let completedAtFormatted = ''
  if (project.completed_at) {
    try {
      const date = new Date(project.completed_at)
      if (!isNaN(date.getTime())) {
        // Форматируем дату в формат YYYY-MM-DD
        completedAtFormatted = date.toISOString().split('T')[0]
      }
    } catch (error) {
      console.warn('Error parsing completed_at date:', project.completed_at, error)
    }
  }

  // Правильная обработка всех полей формы
  form.value = {
    title: project.title || '',
    description: project.description || '',
    short_description: project.short_description || '',
    image_url: project.image_url || '',
    gallery_images: Array.isArray(project.gallery_images) ? [...project.gallery_images] : [],
    project_url: project.project_url || '',
    github_url: project.github_url || '',
    technologies: Array.isArray(project.technologies) ? [...project.technologies] : [],
    portfolio_category_id: categoryId,
    client: project.client || '',
    completed_at: completedAtFormatted,
    is_featured: Boolean(project.is_featured),
    is_published: Boolean(project.is_published),
    sort_order: Number(project.sort_order) || 0
  }
  error.value = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingProject.value = null
  error.value = ''
}

const addGalleryImage = () => {
  form.value.gallery_images.push('')
}

const removeGalleryImage = (index) => {
  form.value.gallery_images.splice(index, 1)
}

const saveProject = async () => {
  try {
    saving.value = true
    error.value = ''

    if (editingProject.value) {
      await adminApiService.updatePortfolio(editingProject.value.id, form.value)
      showSuccess('Project updated successfully!')
    } else {
      await adminApiService.createPortfolio(form.value)
      showSuccess('Project created successfully!')
    }

    closeModal()
    loadProjects(portfolios.value.current_page)
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save project'
    showError(err.response?.data?.message || 'Failed to save project')
    console.error('Failed to save project:', err)
  } finally {
    saving.value = false
  }
}

const deleteProject = async (project, event) => {
  event.stopPropagation()

  if (confirm(`Are you sure you want to delete "${project.title}"? This action cannot be undone.`)) {
    try {
      await adminApiService.deletePortfolio(project.id)
      showSuccess(`Project "${project.title}" deleted successfully`)
      loadProjects(portfolios.value.current_page)
    } catch (error) {
      console.error('Failed to delete project:', error)
      showError('Failed to delete project. Please try again.')
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

onMounted(() => {
  loadProjects()
})
</script>

<!-- Стили находятся в отдельном файле admin-portfolio.scss -->
