<template>
  <div class="admin-careers">
    <div class="admin-header">
      <div class="header-left">
        <h1>Career Management</h1>
        <p class="header-subtitle">Manage job positions and track applications</p>
      </div>
      <button @click="showCreateModal" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add New Position
      </button>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon primary">
          <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ careers.length }}</div>
          <div class="stat-label">Total Positions</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon success">
          <i class="fas fa-eye"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ activeCareers }}</div>
          <div class="stat-label">Active Positions</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon warning">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ urgentPositions }}</div>
          <div class="stat-label">Urgent Hiring</div>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon info">
          <i class="fas fa-globe"></i>
        </div>
        <div class="stat-content">
          <div class="stat-number">{{ remotePositions }}</div>
          <div class="stat-label">Remote Available</div>
        </div>
      </div>
    </div>

    <!-- Filters and Search -->
    <div class="filters-section">
      <div class="search-box">
        <i class="fas fa-search"></i>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search positions..."
          @input="handleSearch"
        >
      </div>
      <div class="filter-controls">
        <select v-model="statusFilter" @change="applyFilters">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
        <select v-model="departmentFilter" @change="applyFilters">
          <option value="">All Departments</option>
          <option v-for="dept in uniqueDepartments" :key="dept" :value="dept">
            {{ dept }}
          </option>
        </select>
        <select v-model="typeFilter" @change="applyFilters">
          <option value="">All Types</option>
          <option value="full-time">Full Time</option>
          <option value="part-time">Part Time</option>
          <option value="contract">Contract</option>
          <option value="internship">Internship</option>
        </select>
        <button @click="clearFilters" class="btn btn-outline">
          <i class="fas fa-times"></i>
          Clear
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading && !careers.length" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Loading career positions...</p>
    </div>

    <!-- Error State -->
    <div v-if="error" class="error-state">
      <div class="error-icon">
        <i class="fas fa-exclamation-triangle"></i>
      </div>
      <h3>Error Loading Careers</h3>
      <p>{{ error }}</p>
      <button @click="loadCareers" class="btn btn-primary">
        <i class="fas fa-retry"></i>
        Try Again
      </button>
    </div>

    <!-- Empty State -->
    <div v-if="!loading && !error && !careers.length" class="empty-state">
      <div class="empty-icon">
        <i class="fas fa-briefcase"></i>
      </div>
      <h3>No Career Positions</h3>
      <p>Start by creating your first job position</p>
      <button @click="showCreateModal" class="btn btn-primary">
        <i class="fas fa-plus"></i>
        Add First Position
      </button>
    </div>

    <!-- Enhanced Careers Table -->
    <div v-if="filteredCareers.length" class="table-container">
      <div class="table-header">
        <h3>Career Positions ({{ filteredCareers.length }})</h3>
        <div class="table-actions">
          <button @click="exportCareers" class="btn btn-outline btn-sm">
            <i class="fas fa-download"></i>
            Export
          </button>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="admin-table">
          <thead>
            <tr>
              <th @click="sortBy('title')" class="sortable">
                Title
                <i class="fas fa-sort" :class="getSortIcon('title')"></i>
              </th>
              <th @click="sortBy('department')" class="sortable">
                Department
                <i class="fas fa-sort" :class="getSortIcon('department')"></i>
              </th>
              <th>Type</th>
              <th>Location</th>
              <th @click="sortBy('is_active')" class="sortable">
                Status
                <i class="fas fa-sort" :class="getSortIcon('is_active')"></i>
              </th>
              <th @click="sortBy('priority')" class="sortable">
                Priority
                <i class="fas fa-sort" :class="getSortIcon('priority')"></i>
              </th>
              <th>Deadline</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="career in paginatedCareers" :key="career.id" class="career-row">
              <td class="career-title">
                <div class="title-content">
                  <strong>{{ career.title }}</strong>
                  <div class="career-meta">
                    <span class="experience-level">{{ formatExperienceLevel(career.experience_level) }}</span>
                    <span v-if="career.salary_range" class="salary-range">{{ career.salary_range }}</span>
                  </div>
                </div>
              </td>
              <td>
                <span class="department-badge">{{ career.department }}</span>
              </td>
              <td>
                <span class="badge" :class="getTypeClass(career.employment_type)">
                  {{ formatEmploymentType(career.employment_type) }}
                </span>
              </td>
              <td class="location-cell">
                <div class="location-content">
                  <span>{{ career.location }}</span>
                  <span v-if="career.remote_available" class="remote-badge">
                    <i class="fas fa-wifi"></i>
                    Remote OK
                  </span>
                </div>
              </td>
              <td>
                <span class="status-badge" :class="career.is_active ? 'active' : 'inactive'">
                  <i class="fas" :class="career.is_active ? 'fa-check-circle' : 'fa-pause-circle'"></i>
                  {{ career.is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td>
                <div class="priority-indicator" :class="getPriorityClass(career.priority)">
                  {{ career.priority }}
                </div>
              </td>
              <td>
                <span v-if="career.application_deadline" class="deadline" :class="getDeadlineClass(career.application_deadline)">
                  <i class="fas fa-calendar"></i>
                  {{ formatDate(career.application_deadline) }}
                </span>
                <span v-else class="no-deadline">No deadline</span>
              </td>
              <td class="actions">
                <div class="action-buttons">
                  <button @click="editCareer(career)" class="btn-icon" title="Edit">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="duplicateCareer(career)" class="btn-icon info" title="Duplicate">
                    <i class="fas fa-copy"></i>
                  </button>
                  <button @click="toggleCareerStatus(career)" class="btn-icon" :class="career.is_active ? 'warning' : 'success'" :title="career.is_active ? 'Deactivate' : 'Activate'">
                    <i class="fas" :class="career.is_active ? 'fa-pause' : 'fa-play'"></i>
                  </button>
                  <button @click="deleteCareer(career)" class="btn-icon danger" title="Delete">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="pagination-container">
        <div class="pagination-info">
          Showing {{ startItem }} to {{ endItem }} of {{ filteredCareers.length }} positions
        </div>
        <div class="pagination">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="btn btn-outline btn-sm"
          >
            <i class="fas fa-chevron-left"></i>
            Previous
          </button>

          <span class="page-numbers">
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="currentPage = page"
              class="btn btn-sm"
              :class="page === currentPage ? 'btn-primary' : 'btn-outline'"
            >
              {{ page }}
            </button>
          </span>

          <button
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="btn btn-outline btn-sm"
          >
            Next
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Enhanced Create/Edit Modal -->
    <div v-if="showModal" class="modal-overlay" @click="closeModal">
      <div class="modal-content large" @click.stop>
        <div class="modal-header">
          <div class="modal-title">
            <h2>{{ isEditing ? 'Edit Position' : 'Create New Position' }}</h2>
            <p class="modal-subtitle">{{ isEditing ? 'Update job position details' : 'Add a new career opportunity' }}</p>
          </div>
          <button @click="closeModal" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <form @submit.prevent="saveCareer" class="modal-body">
          <div class="form-tabs">
            <button
              type="button"
              v-for="tab in formTabs"
              :key="tab.id"
              @click="activeTab = tab.id"
              class="tab-button"
              :class="{ active: activeTab === tab.id }"
            >
              <i :class="tab.icon"></i>
              {{ tab.label }}
            </button>
          </div>

          <!-- Basic Information Tab -->
          <div v-show="activeTab === 'basic'" class="tab-content">
            <div class="form-grid">
              <div class="form-group">
                <label>Position Title *</label>
                <input v-model="form.title" type="text" required placeholder="e.g., Senior Frontend Developer">
                <small class="form-hint">This will be displayed as the main job title</small>
              </div>

              <div class="form-group">
                <label>Department *</label>
                <select v-model="form.department" required>
                  <option value="">Select department</option>
                  <option v-for="(label, key) in organizationData.departments" :key="key" :value="key">
                    {{ label }}
                  </option>
                </select>
                <small class="form-hint">The department this position belongs to</small>
              </div>

              <div class="form-group">
                <label>Employment Type *</label>
                <select v-model="form.employment_type" required>
                  <option value="">Select employment type</option>
                  <option v-for="(label, key) in organizationData.employmentTypes" :key="key" :value="key">
                    {{ label }}
                  </option>
                </select>
              </div>

              <div class="form-group">
                <label>Experience Level *</label>
                <select v-model="form.experience_level" required>
                  <option value="">Select experience level</option>
                  <option v-for="(label, key) in organizationData.experienceLevels" :key="key" :value="key">
                    {{ label }}
                  </option>
                </select>
              </div>

              <div class="form-group">
                <label>Location *</label>
                <select v-model="form.location" required>
                  <option value="">Select location</option>
                  <option v-for="(label, key) in organizationData.locations" :key="key" :value="key">
                    {{ label }}
                  </option>
                </select>
                <small class="form-hint">Primary work location</small>
              </div>

              <div class="form-group">
                <label>Salary Range</label>
                <input v-model="form.salary_range" type="text" placeholder="e.g., $80,000 - $120,000">
                <small class="form-hint">Optional salary range for transparency</small>
              </div>

              <div class="form-group">
                <label>Priority Level</label>
                <select v-model.number="form.priority">
                  <option value="0">Normal (0)</option>
                  <option value="25">Low Priority (25)</option>
                  <option value="50">Medium Priority (50)</option>
                  <option value="75">High Priority (75)</option>
                  <option value="100">Urgent (100)</option>
                </select>
                <small class="form-hint">Higher priority positions appear first</small>
              </div>

              <div class="form-group">
                <label>Application Deadline</label>
                <input v-model="form.application_deadline" type="date" :min="today">
                <small class="form-hint">When applications close for this position</small>
              </div>

              <div class="form-group checkbox-group">
                <label class="checkbox-label">
                  <input v-model="form.remote_available" type="checkbox">
                  <span class="checkmark"></span>
                  Remote Work Available
                </label>
                <small class="form-hint">Check if this position supports remote work</small>
              </div>

              <div class="form-group checkbox-group">
                <label class="checkbox-label">
                  <input v-model="form.is_active" type="checkbox">
                  <span class="checkmark"></span>
                  Active Position
                </label>
                <small class="form-hint">Active positions are visible to applicants</small>
              </div>
            </div>
          </div>

          <!-- Description Tab -->
          <div v-show="activeTab === 'description'" class="tab-content">
            <div class="form-group">
              <label>Short Description *</label>
              <textarea
                v-model="form.short_description"
                rows="3"
                required
                placeholder="Brief summary of the position (2-3 sentences)"
                maxlength="200"
              ></textarea>
              <small class="form-hint">{{ form.short_description?.length || 0 }}/200 characters</small>
            </div>

            <div class="form-group">
              <label>Full Description *</label>
              <textarea
                v-model="form.description"
                rows="8"
                required
                placeholder="Detailed description of the role, responsibilities, and what the candidate will be doing..."
              ></textarea>
              <small class="form-hint">Detailed job description and responsibilities</small>
            </div>

            <div class="form-group">
              <label>Requirements *</label>
              <textarea
                v-model="form.requirements"
                rows="6"
                required
                placeholder="• Bachelor's degree in relevant field&#10;• 3+ years of experience&#10;• Proficiency in specific technologies&#10;• Strong communication skills"
              ></textarea>
              <small class="form-hint">List the required qualifications and skills</small>
            </div>

            <div class="form-group">
              <label>Benefits & Perks</label>
              <textarea
                v-model="form.benefits"
                rows="4"
                placeholder="• Competitive salary&#10;• Health insurance&#10;• Flexible working hours&#10;• Professional development opportunities"
              ></textarea>
              <small class="form-hint">What benefits and perks does this position offer?</small>
            </div>
          </div>

          <!-- Skills Tab -->
          <div v-show="activeTab === 'skills'" class="tab-content">
            <div class="form-group">
              <label>Required Skills</label>
              <div class="skills-input">
                <input
                  v-model="newSkill"
                  type="text"
                  placeholder="Type a skill and press Enter"
                  @keypress.enter.prevent="addSkill"
                >
                <button type="button" @click="addSkill" class="btn btn-outline btn-sm">
                  <i class="fas fa-plus"></i>
                  Add
                </button>
              </div>
              <div v-if="form.skills.length" class="skills-list">
                <span
                  v-for="(skill, index) in form.skills"
                  :key="index"
                  class="skill-tag"
                >
                  {{ skill }}
                  <button type="button" @click="removeSkill(index)" class="remove-skill">
                    <i class="fas fa-times"></i>
                  </button>
                </span>
              </div>
              <small class="form-hint">Add relevant technical and soft skills for this position</small>
            </div>

            <div class="form-group">
              <label>Suggested Skills</label>
              <div class="suggested-skills">
                <button
                  type="button"
                  v-for="skill in suggestedSkills"
                  :key="skill"
                  @click="addSuggestedSkill(skill)"
                  class="skill-suggestion"
                  :disabled="form.skills.includes(skill)"
                >
                  {{ skill }}
                </button>
              </div>
            </div>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn btn-secondary">
              <i class="fas fa-times"></i>
              Cancel
            </button>
            <button type="submit" class="btn btn-primary" :disabled="modalLoading">
              <i class="fas" :class="modalLoading ? 'fa-spinner fa-spin' : 'fa-save'"></i>
              {{ modalLoading ? 'Saving...' : (isEditing ? 'Update Position' : 'Create Position') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Toast Notifications -->
    <div v-if="toast.show" class="toast" :class="toast.type">
      <div class="toast-content">
        <i class="fas" :class="getToastIcon(toast.type)"></i>
        <span>{{ toast.message }}</span>
      </div>
      <button @click="hideToast" class="toast-close">
        <i class="fas fa-times"></i>
      </button>
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
    const modalLoading = ref(false)
    const error = ref('')
    const searchQuery = ref('')
    const statusFilter = ref('')
    const departmentFilter = ref('')
    const typeFilter = ref('')
    const activeTab = ref('basic')
    const newSkill = ref('')
    const currentPage = ref(1)
    const itemsPerPage = 10
    const sortKey = ref('id')
    const sortOrder = ref('desc')
    const toast = ref({ show: false, message: '', type: '' })

    // Organization data
    const organizationData = ref({
      departments: {},
      positions: {},
      skills: {},
      employmentTypes: {},
      experienceLevels: {},
      locations: {},
      statuses: {}
    })

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

    const formTabs = ref([
      { id: 'basic', label: 'Basic Info', icon: 'fas fa-info-circle' },
      { id: 'description', label: 'Description', icon: 'fas fa-file-text' },
      { id: 'skills', label: 'Skills', icon: 'fas fa-cogs' }
    ])

    // Dynamic suggested skills from organization data
    const suggestedSkills = computed(() => {
      return Object.values(organizationData.value.skills)
    })

    // Computed properties
    const activeCareers = computed(() =>
      careers.value.filter(career => career.is_active).length
    )

    const urgentPositions = computed(() =>
      careers.value.filter(career => career.priority >= 75).length
    )

    const remotePositions = computed(() =>
      careers.value.filter(career => career.remote_available).length
    )

    const uniqueDepartments = computed(() => {
      const departments = new Set(careers.value.map(career => career.department))
      return Array.from(departments).sort()
    })

    const filteredCareers = computed(() => {
      let filtered = careers.value.filter(career => {
        const matchesStatus = statusFilter.value ?
          career.is_active === (statusFilter.value === 'active') : true
        const matchesDepartment = departmentFilter.value ?
          career.department === departmentFilter.value : true
        const matchesType = typeFilter.value ?
          career.employment_type === typeFilter.value : true
        const matchesSearch = searchQuery.value ?
          career.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          career.department.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          career.location.toLowerCase().includes(searchQuery.value.toLowerCase()) : true

        return matchesStatus && matchesDepartment && matchesType && matchesSearch
      })

      // Apply sorting
      return filtered.sort((a, b) => {
        const modifier = sortOrder.value === 'asc' ? 1 : -1
        if (a[sortKey.value] < b[sortKey.value]) return -1 * modifier
        if (a[sortKey.value] > b[sortKey.value]) return 1 * modifier
        return 0
      })
    })

    const totalPages = computed(() =>
      Math.ceil(filteredCareers.value.length / itemsPerPage)
    )

    const paginatedCareers = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage
      const end = start + itemsPerPage
      return filteredCareers.value.slice(start, end)
    })

    const startItem = computed(() =>
      (currentPage.value - 1) * itemsPerPage + 1
    )

    const endItem = computed(() =>
      Math.min(currentPage.value * itemsPerPage, filteredCareers.value.length)
    )

    const visiblePages = computed(() => {
      const pages = []
      const maxVisible = 5
      let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
      let end = Math.min(totalPages.value, start + maxVisible - 1)

      if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1)
      }

      for (let i = start; i <= end; i++) {
        pages.push(i)
      }
      return pages
    })

    const today = computed(() => {
      return new Date().toISOString().split('T')[0]
    })

    // Methods
    const getAuthHeaders = () => {
      const token = localStorage.getItem('admin_token')
      return token ? { Authorization: `Bearer ${token}` } : {}
    }

    const loadOrganizationData = async () => {
      try {
        // Load all organization data types in parallel
        const [departments, positions, skills, employmentTypes, experienceLevels, locations, statuses] = await Promise.all([
          axios.get('/api/organization/departments').catch(() => ({ data: { data: {} } })),
          axios.get('/api/organization/positions').catch(() => ({ data: { data: {} } })),
          axios.get('/api/organization/skills').catch(() => ({ data: { data: {} } })),
          axios.get('/api/organization/employment-types').catch(() => ({ data: { data: {} } })),
          axios.get('/api/organization/experience-levels').catch(() => ({ data: { data: {} } })),
          axios.get('/api/organization/locations').catch(() => ({ data: { data: {} } })),
          axios.get('/api/organization/statuses').catch(() => ({ data: { data: {} } }))
        ])

        organizationData.value = {
          departments: departments.data.data || getDefaultDepartments(),
          positions: positions.data.data || getDefaultPositions(),
          skills: skills.data.data || getDefaultSkills(),
          employmentTypes: employmentTypes.data.data || getDefaultEmploymentTypes(),
          experienceLevels: experienceLevels.data.data || getDefaultExperienceLevels(),
          locations: locations.data.data || getDefaultLocations(),
          statuses: statuses.data.data || getDefaultStatuses()
        }
      } catch (err) {
        console.error('Failed to load organization data:', err)
        // Use fallback values if all API calls fail
        organizationData.value = {
          departments: getDefaultDepartments(),
          positions: getDefaultPositions(),
          skills: getDefaultSkills(),
          employmentTypes: getDefaultEmploymentTypes(),
          experienceLevels: getDefaultExperienceLevels(),
          locations: getDefaultLocations(),
          statuses: getDefaultStatuses()
        }
      }
    }

    const getDefaultDepartments = () => ({
      'engineering': 'Engineering',
      'design': 'Design',
      'marketing': 'Marketing',
      'sales': 'Sales',
      'hr': 'Human Resources',
      'finance': 'Finance',
      'operations': 'Operations',
      'customer-support': 'Customer Support'
    })

    const getDefaultPositions = () => ({
      'frontend-developer': 'Frontend Developer',
      'backend-developer': 'Backend Developer',
      'fullstack-developer': 'Full Stack Developer',
      'ui-ux-designer': 'UI/UX Designer',
      'product-manager': 'Product Manager',
      'marketing-manager': 'Marketing Manager',
      'sales-representative': 'Sales Representative',
      'hr-specialist': 'HR Specialist',
      'devops-engineer': 'DevOps Engineer',
      'data-analyst': 'Data Analyst'
    })

    const getDefaultEmploymentTypes = () => ({
      'full-time': 'Full Time',
      'part-time': 'Part Time',
      'contract': 'Contract',
      'internship': 'Internship',
      'freelance': 'Freelance',
      'temporary': 'Temporary'
    })

    const getDefaultExperienceLevels = () => ({
      'entry': 'Entry Level',
      'junior': 'Junior',
      'mid': 'Mid Level',
      'senior': 'Senior',
      'lead': 'Lead',
      'principal': 'Principal',
      'director': 'Director'
    })

    const getDefaultLocations = () => ({
      'remote': 'Remote',
      'new-york': 'New York, NY',
      'san-francisco': 'San Francisco, CA',
      'london': 'London, UK',
      'berlin': 'Berlin, Germany',
      'toronto': 'Toronto, Canada',
      'sydney': 'Sydney, Australia',
      'tokyo': 'Tokyo, Japan'
    })

    const getDefaultStatuses = () => ({
      'active': 'Active',
      'inactive': 'Inactive',
      'draft': 'Draft',
      'archived': 'Archived'
    })

    const getDefaultSkills = () => ({
      'javascript': 'JavaScript',
      'typescript': 'TypeScript',
      'php': 'PHP',
      'laravel': 'Laravel',
      'vuejs': 'Vue.js',
      'react': 'React',
      'nodejs': 'Node.js',
      'python': 'Python',
      'mysql': 'MySQL',
      'postgresql': 'PostgreSQL',
      'mongodb': 'MongoDB',
      'git': 'Git',
      'docker': 'Docker',
      'aws': 'AWS',
      'html': 'HTML',
      'css': 'CSS',
      'sass': 'SASS/SCSS',
      'tailwindcss': 'Tailwind CSS',
      'bootstrap': 'Bootstrap',
      'figma': 'Figma',
      'photoshop': 'Photoshop',
      'sketch': 'Sketch',
      'communication': 'Communication',
      'teamwork': 'Team Leadership',
      'problem-solving': 'Problem Solving',
      'project-management': 'Project Management',
      'agile': 'Agile/Scrum',
      'testing': 'Testing',
      'api-development': 'API Development',
      'mobile-development': 'Mobile Development',
      'devops': 'DevOps',
      'cybersecurity': 'Cybersecurity'
    })

    // Methods
    const loadCareers = async () => {
      loading.value = true
      error.value = ''
      try {
        const response = await axios.get('/api/careers', {
          headers: getAuthHeaders()
        })
        careers.value = response.data.data || []
      } catch (err) {
        console.error('Failed to load careers:', err)
        error.value = 'Failed to load careers. Please try again later.'
      } finally {
        loading.value = false
      }
    }

    const showCreateModal = () => {
      resetForm()
      isEditing.value = false
      activeTab.value = 'basic'
      showModal.value = true
    }

    const editCareer = (career) => {
      Object.keys(form.value).forEach(key => {
        if (key === 'skills') {
          form.value[key] = career[key] ? [...career[key]] : []
        } else {
          form.value[key] = career[key] !== undefined ? career[key] : form.value[key]
        }
      })
      isEditing.value = true
      activeTab.value = 'basic'
      showModal.value = true
    }

    const duplicateCareer = (career) => {
      Object.keys(form.value).forEach(key => {
        if (key === 'skills') {
          form.value[key] = career[key] ? [...career[key]] : []
        } else if (key === 'title') {
          form.value[key] = `Copy of ${career[key]}`
        } else if (key === 'is_active') {
          form.value[key] = false // New duplicates start as inactive
        } else {
          form.value[key] = career[key] !== undefined ? career[key] : form.value[key]
        }
      })
      isEditing.value = false
      activeTab.value = 'basic'
      showModal.value = true
    }

    const saveCareer = async () => {
      modalLoading.value = true
      try {
        // Validate form
        if (!form.value.title || !form.value.department || !form.value.employment_type) {
          showToast('Please fill in all required fields', 'error')
          return
        }

        const url = isEditing.value
          ? `/api/admin/careers/${form.value.id}`
          : '/api/admin/careers'

        const method = isEditing.value ? 'put' : 'post'

        await axios[method](url, form.value, {
          headers: getAuthHeaders()
        })

        closeModal()
        await loadCareers()
        showToast(
          isEditing.value ? 'Position updated successfully!' : 'Position created successfully!',
          'success'
        )
      } catch (err) {
        console.error('Failed to save career:', err)
        showToast('Failed to save position. Please check your input and try again.', 'error')
      } finally {
        modalLoading.value = false
      }
    }

    const deleteCareer = async (career) => {
      if (!confirm(`Are you sure you want to delete "${career.title}"? This action cannot be undone.`)) {
        return
      }

      try {
        await axios.delete(`/api/admin/careers/${career.id}`, {
          headers: getAuthHeaders()
        })
        await loadCareers()
        showToast('Position deleted successfully!', 'success')
      } catch (err) {
        console.error('Failed to delete career:', err)
        showToast('Failed to delete position. Please try again later.', 'error')
      }
    }

    const toggleCareerStatus = async (career) => {
      try {
        await axios.put(`/api/admin/careers/${career.id}`, {
          ...career,
          is_active: !career.is_active
        }, {
          headers: getAuthHeaders()
        })
        await loadCareers()
        showToast(
          `Position ${!career.is_active ? 'activated' : 'deactivated'} successfully!`,
          'success'
        )
      } catch (err) {
        console.error('Failed to toggle career status:', err)
        showToast('Failed to update position status.', 'error')
      }
    }

    const exportCareers = () => {
      const csvContent = [
        'Title,Department,Type,Location,Status,Priority,Remote,Deadline',
        ...filteredCareers.value.map(career =>
          `"${career.title}","${career.department}","${formatEmploymentType(career.employment_type)}","${career.location}","${career.is_active ? 'Active' : 'Inactive'}",${career.priority},"${career.remote_available ? 'Yes' : 'No'}","${career.application_deadline || 'No deadline'}"`
        )
      ].join('\n')

      const blob = new Blob([csvContent], { type: 'text/csv' })
      const url = window.URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `careers-${new Date().toISOString().split('T')[0]}.csv`
      a.click()
      window.URL.revokeObjectURL(url)
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
      newSkill.value = ''
    }

    const addSkill = () => {
      const skill = newSkill.value.trim()
      if (skill && !form.value.skills.includes(skill)) {
        form.value.skills.push(skill)
        newSkill.value = ''
      }
    }

    const removeSkill = (index) => {
      form.value.skills.splice(index, 1)
    }

    const addSuggestedSkill = (skill) => {
      if (!form.value.skills.includes(skill)) {
        form.value.skills.push(skill)
      }
    }

    // Utility methods
    const getTypeClass = (type) => {
      const classes = {
        'full-time': 'success',
        'part-time': 'warning',
        'contract': 'info',
        'internship': 'secondary'
      }
      return classes[type] || 'secondary'
    }

    const formatExperienceLevel = (level) => {
      const levels = {
        'entry': 'Entry Level',
        'junior': 'Junior',
        'mid': 'Mid Level',
        'senior': 'Senior',
        'lead': 'Lead',
        'principal': 'Principal'
      }
      return levels[level] || level
    }

    const formatEmploymentType = (type) => {
      const types = {
        'full-time': 'Full Time',
        'part-time': 'Part Time',
        'contract': 'Contract',
        'internship': 'Internship'
      }
      return types[type] || type
    }

    const formatDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      })
    }

    const getPriorityClass = (priority) => {
      if (priority === 0) return 'normal'
      if (priority <= 25) return 'low'
      if (priority <= 50) return 'medium'
      if (priority <= 75) return 'high'
      return 'urgent'
    }

    const getDeadlineClass = (deadline) => {
      if (!deadline) return ''

      const now = new Date()
      const date = new Date(deadline)
      const diff = date - now

      if (diff < 0) return 'past'
      if (diff < 7 * 24 * 60 * 60 * 1000) return 'urgent'
      return 'normal'
    }

    const getSortIcon = (key) => {
      if (sortKey.value !== key) return ''
      return sortOrder.value === 'asc' ? 'fa-sort-up' : 'fa-sort-down'
    }

    const sortBy = (key) => {
      if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
      } else {
        sortKey.value = key
        sortOrder.value = 'asc'
      }
    }

    const handleSearch = () => {
      currentPage.value = 1
    }

    const applyFilters = () => {
      currentPage.value = 1
    }

    const clearFilters = () => {
      searchQuery.value = ''
      statusFilter.value = ''
      departmentFilter.value = ''
      typeFilter.value = ''
      currentPage.value = 1
    }

    const showToast = (message, type = 'success') => {
      toast.value = { show: true, message, type }
      setTimeout(() => {
        toast.value.show = false
      }, 4000)
    }

    const hideToast = () => {
      toast.value.show = false
    }

    const getToastIcon = (type) => {
      const icons = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
      }
      return icons[type] || 'fa-info-circle'
    }

    // Utility methods for getting organization data labels
    const getDepartmentLabel = (key) => {
      return organizationData.value.departments[key] || key
    }

    const getPositionLabel = (key) => {
      return organizationData.value.positions[key] || key
    }

    const getEmploymentTypeLabel = (key) => {
      return organizationData.value.employmentTypes[key] || formatEmploymentType(key)
    }

    const getExperienceLevelLabel = (key) => {
      return organizationData.value.experienceLevels[key] || formatExperienceLevel(key)
    }

    const getLocationLabel = (key) => {
      return organizationData.value.locations[key] || key
    }

    const getSkillLabel = (key) => {
      return organizationData.value.skills[key] || key
    }

    // Lifecycle
    onMounted(() => {
      loadCareers()
      loadOrganizationData()

      // Handle escape key for modal
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && showModal.value) {
          closeModal()
        }
      })
    })

    return {
      careers,
      showModal,
      isEditing,
      loading,
      modalLoading,
      error,
      searchQuery,
      statusFilter,
      departmentFilter,
      typeFilter,
      activeTab,
      newSkill,
      currentPage,
      itemsPerPage,
      sortKey,
      sortOrder,
      toast,
      form,
      formTabs,
      suggestedSkills,
      activeCareers,
      urgentPositions,
      remotePositions,
      uniqueDepartments,
      filteredCareers,
      totalPages,
      paginatedCareers,
      startItem,
      endItem,
      visiblePages,
      today,
      showCreateModal,
      editCareer,
      duplicateCareer,
      saveCareer,
      deleteCareer,
      toggleCareerStatus,
      exportCareers,
      closeModal,
      addSkill,
      removeSkill,
      addSuggestedSkill,
      getTypeClass,
      formatExperienceLevel,
      formatEmploymentType,
      formatDate,
      getPriorityClass,
      getDeadlineClass,
      getSortIcon,
      sortBy,
      handleSearch,
      applyFilters,
      clearFilters,
      showToast,
      hideToast,
      getToastIcon,
      organizationData,
      getDepartmentLabel,
      getPositionLabel,
      getEmploymentTypeLabel,
      getExperienceLevelLabel,
      getLocationLabel,
      getSkillLabel
    }
  }
}
</script>

<style scoped>
/* Admin Careers Styles */
.admin-careers {
  padding: 24px;
  background: #f8fafc;
  min-height: 100vh;
}

.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 32px;
  background: white;
  padding: 24px;
  border-radius: 12px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.header-left h1 {
  font-size: 28px;
  font-weight: 700;
  color: #1a202c;
  margin: 0 0 8px 0;
}

.header-subtitle {
  font-size: 16px;
  color: #718096;
  margin: 0;
  line-height: 1.5;
}

.btn {
  border-radius: 8px;
  border: none;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 12px 20px;
  box-shadow: 0 4px 14px 0 rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px 0 rgba(102, 126, 234, 0.4);
}

.btn-secondary {
  background: #e2e8f0;
  color: #4a5568;
  padding: 12px 20px;
}

.btn-secondary:hover {
  background: #cbd5e0;
}

.btn-outline {
  background: transparent;
  color: #4a5568;
  border: 2px solid #e2e8f0;
  padding: 10px 18px;
}

.btn-outline:hover {
  background: #f7fafc;
  border-color: #cbd5e0;
}

.btn-sm {
  padding: 8px 16px;
  font-size: 13px;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 24px;
  margin-bottom: 32px;
}

.stat-card {
  background: white;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  width: 60px;
  height: 60px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}

.stat-icon.primary { background: #e6fffa; color: #319795; }
.stat-icon.success { background: #f0fff4; color: #38a169; }
.stat-icon.warning { background: #fffbeb; color: #d69e2e; }
.stat-icon.info { background: #ebf8ff; color: #3182ce; }

.stat-content {
  flex: 1;
}

.stat-number {
  font-size: 28px;
  font-weight: 700;
  color: #1a202c;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 14px;
  color: #718096;
  font-weight: 500;
}

/* Filters */
.filters-section {
  background: white;
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.search-box {
  position: relative;
  margin-bottom: 16px;
}

.search-box i {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #a0aec0;
  font-size: 16px;
}

.search-box input {
  width: 100%;
  padding: 12px 16px 12px 48px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 16px;
  background: #f7fafc;
  transition: all 0.2s ease;
}

.search-box input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.filter-controls {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
  align-items: center;
}

.filter-controls select {
  padding: 10px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  background: white;
  font-size: 14px;
  color: #4a5568;
  min-width: 150px;
}

.filter-controls select:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Loading, Error, Empty States */
.loading-state, .error-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 64px 24px;
  background: white;
  border-radius: 12px;
  text-align: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.loading-spinner {
  width: 48px;
  height: 48px;
  border: 4px solid #e2e8f0;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-icon, .empty-icon {
  font-size: 64px;
  margin-bottom: 16px;
  opacity: 0.5;
}

.error-state .error-icon { color: #e53e3e; }
.empty-state .empty-icon { color: #a0aec0; }

/* Table Styles */
.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e2e8f0;
  background: #f7fafc;
}

.table-header h3 {
  font-size: 18px;
  font-weight: 600;
  color: #1a202c;
  margin: 0;
}

.table-wrapper {
  overflow-x: auto;
}

.admin-table {
  width: 100%;
  border-collapse: collapse;
}

.admin-table th {
  background: #f7fafc;
  padding: 16px 20px;
  text-align: left;
  font-weight: 600;
  color: #4a5568;
  font-size: 14px;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}

.admin-table th.sortable {
  cursor: pointer;
  user-select: none;
  transition: all 0.2s ease;
}

.admin-table th.sortable:hover {
  background: #edf2f7;
  color: #2d3748;
}

.admin-table th i {
  margin-left: 8px;
  opacity: 0.5;
  transition: opacity 0.2s ease;
}

.admin-table th.sortable:hover i {
  opacity: 1;
}

.admin-table td {
  padding: 16px 20px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.career-row {
  transition: background-color 0.2s ease;
}

.career-row:hover {
  background: #f8fafc;
}

/* Career specific styles */
.career-title .title-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.career-meta {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.experience-level, .salary-range {
  font-size: 12px;
  color: #718096;
  background: #f7fafc;
  padding: 2px 6px;
  border-radius: 4px;
}

.department-badge {
  display: inline-block;
  padding: 6px 12px;
  background: #ebf8ff;
  color: #3182ce;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 500;
}

.badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.badge.success { background: #f0fff4; color: #38a169; }
.badge.warning { background: #fffbeb; color: #d69e2e; }
.badge.info { background: #ebf8ff; color: #3182ce; }
.badge.secondary { background: #edf2f7; color: #4a5568; }

.location-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.remote-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  color: #38a169;
  background: #f0fff4;
  padding: 2px 6px;
  border-radius: 4px;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.status-badge.active {
  background: #f0fff4;
  color: #38a169;
}

.status-badge.inactive {
  background: #fed7d7;
  color: #e53e3e;
}

.priority-indicator {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  min-width: 40px;
  text-align: center;
}

.priority-indicator.normal { background: #edf2f7; color: #4a5568; }
.priority-indicator.low { background: #e6fffa; color: #319795; }
.priority-indicator.medium { background: #fffbeb; color: #d69e2e; }
.priority-indicator.high { background: #fed7d7; color: #e53e3e; }
.priority-indicator.urgent { background: #742a2a; color: white; }

.deadline {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
}

.deadline.normal { color: #4a5568; }
.deadline.urgent { color: #e53e3e; font-weight: 600; }
.deadline.past { color: #a0aec0; text-decoration: line-through; }

.no-deadline {
  font-size: 12px;
  color: #a0aec0;
  font-style: italic;
}

/* Action buttons */
.actions {
  width: 140px;
}

.action-buttons {
  display: flex;
  gap: 6px;
  justify-content: flex-end;
}

.btn-icon {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  border: none;
  background: #f7fafc;
  color: #4a5568;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-icon:hover {
  background: #edf2f7;
  transform: translateY(-1px);
}

.btn-icon.info { color: #3182ce; }
.btn-icon.info:hover { background: #bee3f8; }
.btn-icon.success { color: #38a169; }
.btn-icon.success:hover { background: #c6f6d5; }
.btn-icon.warning { color: #d69e2e; }
.btn-icon.warning:hover { background: #faf089; }
.btn-icon.danger { color: #e53e3e; }
.btn-icon.danger:hover { background: #fed7d7; }

/* Pagination */
.pagination-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-top: 1px solid #e2e8f0;
  background: #f7fafc;
}

.pagination-info {
  font-size: 14px;
  color: #718096;
}

.pagination {
  display: flex;
  align-items: center;
  gap: 8px;
}

.page-numbers {
  display: flex;
  gap: 4px;
}

/* Modal Styles */
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

.modal-content {
  background: white;
  border-radius: 16px;
  width: 90%;
  max-width: 900px;
  max-height: 90vh;
  overflow: hidden;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
  position: relative;
}

.modal-content.large {
  max-width: 1000px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 24px;
  border-bottom: 1px solid #e2e8f0;
  background: #f7fafc;
}

.modal-title h2 {
  font-size: 24px;
  font-weight: 700;
  color: #1a202c;
  margin: 0 0 8px 0;
}

.modal-subtitle {
  font-size: 16px;
  color: #718096;
  margin: 0;
}

.btn-close {
  background: #edf2f7;
  border: none;
  border-radius: 8px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #4a5568;
}

.btn-close:hover {
  background: #e2e8f0;
  color: #2d3748;
}

.modal-body {
  padding: 24px;
  max-height: 60vh;
  overflow-y: auto;
}

/* Form Tabs */
.form-tabs {
  display: flex;
  border-bottom: 2px solid #e2e8f0;
  margin-bottom: 24px;
  background: #f7fafc;
  border-radius: 8px 8px 0 0;
  overflow: hidden;
}

.tab-button {
  flex: 1;
  padding: 16px 20px;
  text-align: center;
  cursor: pointer;
  font-weight: 600;
  color: #718096;
  background: transparent;
  border: none;
  transition: all 0.2s ease;
  position: relative;
}

.tab-button:hover {
  background: #edf2f7;
  color: #4a5568;
}

.tab-button.active {
  background: white;
  color: #667eea;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.tab-button.active::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: #667eea;
}

.tab-button i {
  margin-right: 8px;
  font-size: 16px;
}

.tab-content {
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Form Styles */
.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 8px;
  font-weight: 600;
  color: #2d3748;
  font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
  padding: 12px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 14px;
  transition: all 0.2s ease;
  background: #f7fafc;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 80px;
}

.form-hint {
  margin-top: 6px;
  font-size: 12px;
  color: #718096;
  line-height: 1.4;
}

.checkbox-group {
  flex-direction: row;
  align-items: center;
  gap: 12px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-weight: 500;
  color: #4a5568;
}

.checkbox-label input[type="checkbox"] {
  width: 18px;
  height: 18px;
  accent-color: #667eea;
}

/* Skills */
.skills-input {
  display: flex;
  gap: 12px;
  align-items: flex-end;
}

.skills-input input {
  flex: 1;
}

.skills-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
}

.skill-tag {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: #667eea;
  color: white;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
}

.remove-skill {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  padding: 0;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
}

.remove-skill:hover {
  background: rgba(255, 255, 255, 0.2);
}

.suggested-skills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 12px;
}

.skill-suggestion {
  padding: 8px 12px;
  background: #f7fafc;
  color: #4a5568;
  border: 2px solid #e2e8f0;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.skill-suggestion:hover:not(:disabled) {
  background: #edf2f7;
  border-color: #cbd5e0;
}

.skill-suggestion:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  background: #e2e8f0;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding: 24px;
  border-top: 1px solid #e2e8f0;
  background: #f7fafc;
}

/* Toast Notifications */
.toast {
  position: fixed;
  top: 24px;
  right: 24px;
  background: white;
  border-radius: 12px;
  padding: 16px 20px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  z-index: 1001;
  display: flex;
  align-items: center;
  gap: 12px;
  max-width: 400px;
  animation: slideIn 0.3s ease;
}

@keyframes slideIn {
  from { transform: translateX(100%); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.toast.success { border-left: 4px solid #38a169; }
.toast.error { border-left: 4px solid #e53e3e; }
.toast.warning { border-left: 4px solid #d69e2e; }
.toast.info { border-left: 4px solid #3182ce; }

.toast-content {
  flex: 1;
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
  font-weight: 500;
  color: #2d3748;
}

.toast-content i {
  font-size: 16px;
}

.toast.success .toast-content i { color: #38a169; }
.toast.error .toast-content i { color: #e53e3e; }
.toast.warning .toast-content i { color: #d69e2e; }
.toast.info .toast-content i { color: #3182ce; }

.toast-close {
  background: none;
  border: none;
  cursor: pointer;
  color: #a0aec0;
  padding: 4px;
  border-radius: 4px;
  transition: all 0.2s ease;
}

.toast-close:hover {
  background: #f7fafc;
  color: #4a5568;
}

/* Responsive Design */
@media (max-width: 768px) {
  .admin-careers { padding: 16px; }

  .admin-header {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }

  .stats-grid {
    grid-template-columns: 1fr;
  }

  .filter-controls {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-controls select {
    min-width: auto;
  }

  .table-wrapper {
    overflow-x: scroll;
  }

  .action-buttons {
    flex-direction: column;
    gap: 4px;
  }

  .pagination-container {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }

  .modal-content {
    width: 95%;
    margin: 20px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .modal-actions {
    flex-direction: column-reverse;
  }

  .toast {
    right: 16px;
    left: 16px;
    max-width: none;
  }
}

@media (max-width: 480px) {
  .form-tabs {
    flex-direction: column;
  }

  .tab-button {
    text-align: left;
    padding: 12px 16px;
  }

  .skills-input {
    flex-direction: column;
    align-items: stretch;
  }
}
</style>
