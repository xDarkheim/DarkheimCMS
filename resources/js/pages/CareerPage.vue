<template>
  <div class="career-page page-with-header-offset">
    <!-- Hero Section -->
    <section class="career-hero">
      <div class="container">
        <div class="career-hero__content">
          <h1 class="career-hero__title">Join Our Team</h1>
          <p class="career-hero__subtitle">
            We're looking for talented individuals to help us build the future of web development.
            Discover exciting opportunities and grow your career with us.
          </p>
        </div>
      </div>
    </section>

    <!-- Enhanced Stats Section -->
    <section class="career-stats" v-if="!loading">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ totalPositions }}</div>
              <div class="stat-label">Total Positions</div>
              <div class="stat-detail">{{ activePositions }} currently open</div>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ urgentPositions }}</div>
              <div class="stat-label">Urgent Hiring</div>
              <div class="stat-detail">High priority roles</div>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-wifi"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ remotePositions }}</div>
              <div class="stat-label">Remote Available</div>
              <div class="stat-detail">Work from anywhere</div>
            </div>
          </div>
          <div class="stat-item">
            <div class="stat-icon">
              <i class="fas fa-building"></i>
            </div>
            <div class="stat-content">
              <div class="stat-number">{{ departments.length }}</div>
              <div class="stat-label">Departments</div>
              <div class="stat-detail">Diverse opportunities</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Filters Section -->
    <section class="career-filters" v-if="totalPositions > 0">
      <div class="container">
        <div class="filters-wrapper">
          <div class="filter-header">
            <h3>Find Your Perfect Role</h3>
            <p>Filter positions to match your preferences</p>
          </div>

          <div class="filter-controls">
            <div class="search-filter">
              <div class="search-box">
                <i class="fas fa-search"></i>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search positions, skills, or departments..."
                  @input="applyFilters"
                >
              </div>
            </div>

            <div class="filter-grid">
              <div class="filter-group">
                <label>Department</label>
                <select v-model="departmentFilter" @change="applyFilters">
                  <option value="">All Departments</option>
                  <option v-for="dept in departments" :key="dept" :value="dept">
                    {{ dept }}
                  </option>
                </select>
              </div>

              <div class="filter-group">
                <label>Employment Type</label>
                <select v-model="typeFilter" @change="applyFilters">
                  <option value="">All Types</option>
                  <option value="full-time">Full Time</option>
                  <option value="part-time">Part Time</option>
                  <option value="contract">Contract</option>
                  <option value="internship">Internship</option>
                </select>
              </div>

              <div class="filter-group">
                <label>Experience Level</label>
                <select v-model="experienceFilter" @change="applyFilters">
                  <option value="">All Levels</option>
                  <option value="entry">Entry Level</option>
                  <option value="junior">Junior</option>
                  <option value="mid">Mid Level</option>
                  <option value="senior">Senior</option>
                  <option value="lead">Lead</option>
                  <option value="principal">Principal</option>
                </select>
              </div>

              <div class="filter-group">
                <label>Location</label>
                <select v-model="locationFilter" @change="applyFilters">
                  <option value="">All Locations</option>
                  <option value="remote">Remote Only</option>
                  <option v-for="loc in locations" :key="loc" :value="loc">
                    {{ loc }}
                  </option>
                </select>
              </div>
            </div>

            <div class="filter-actions">
              <button v-if="hasActiveFilters" @click="clearFilters" class="btn btn--ghost">
                <i class="fas fa-times"></i>
                Clear All Filters
              </button>
              <div class="sort-control">
                <label>Sort by</label>
                <select v-model="sortBy" @change="applyFilters">
                  <option value="priority">Priority</option>
                  <option value="title">Job Title</option>
                  <option value="department">Department</option>
                  <option value="created_at">Newest First</option>
                  <option value="deadline">Application Deadline</option>
                </select>
              </div>
            </div>
          </div>

          <div class="filter-results" v-if="hasActiveFilters">
            <span class="results-count">
              {{ filteredCareers.length }} of {{ totalPositions }} positions match your criteria
            </span>
            <div v-if="filteredCareers.length === 0" class="no-results-hint">
              Try adjusting your filters to see more results
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Job Positions Section -->
    <section class="career-positions">
      <div class="container">
        <!-- Loading State -->
        <div v-if="loading" class="loading-state">
          <div class="loading-spinner"></div>
          <p>Loading job positions...</p>
        </div>

        <!-- Job Listings -->
        <div v-else-if="filteredCareers.length > 0" class="jobs-section">
          <div class="section-header">
            <h2>
              {{ hasActiveFilters ? 'Filtered Results' : 'Open Positions' }}
              <span class="position-count">({{ filteredCareers.length }})</span>
            </h2>
            <p v-if="!hasActiveFilters">Explore our current job openings and find your perfect match</p>
          </div>

          <div class="jobs-grid">
            <div
              v-for="job in filteredCareers"
              :key="job.id"
              class="job-card"
              :class="{
                'job-card--urgent': job.priority >= 75,
                'job-card--featured': job.priority >= 90
              }"
              @click="showJobDetails(job)"
            >
              <!-- Priority Badge -->
              <div v-if="job.priority >= 75" class="job-card__priority">
                <i class="fas fa-bolt"></i>
                {{ job.priority >= 90 ? 'Featured' : 'Urgent' }}
              </div>

              <!-- Deadline Warning -->
              <div v-if="job.application_deadline && isDeadlineSoon(job.application_deadline)"
                   class="job-card__deadline-warning">
                <i class="fas fa-clock"></i>
                Deadline: {{ formatDate(job.application_deadline) }}
              </div>

              <div class="job-card__header">
                <h3 class="job-card__title">{{ job.title }}</h3>
                <div class="job-card__department">{{ job.department }}</div>
                <div v-if="job.salary_range" class="job-card__salary">{{ job.salary_range }}</div>
              </div>

              <div class="job-card__meta">
                <div class="job-meta-item">
                  <i class="fas fa-briefcase"></i>
                  <span>{{ formatEmploymentType(job.employment_type) }}</span>
                </div>
                <div class="job-meta-item">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>{{ job.location }}</span>
                  <span v-if="job.remote_available" class="remote-badge">
                    <i class="fas fa-wifi"></i>
                    Remote OK
                  </span>
                </div>
                <div class="job-meta-item">
                  <i class="fas fa-chart-line"></i>
                  <span>{{ formatExperienceLevel(job.experience_level) }}</span>
                </div>
                <div v-if="job.application_deadline" class="job-meta-item">
                  <i class="fas fa-calendar"></i>
                  <span>Apply by {{ formatDate(job.application_deadline) }}</span>
                </div>
              </div>

              <div class="job-card__description">
                <p>{{ job.short_description }}</p>
              </div>

              <div v-if="job.skills && job.skills.length" class="job-card__skills">
                <span
                  v-for="skill in job.skills.slice(0, 5)"
                  :key="skill"
                  class="skill-tag"
                >
                  {{ skill }}
                </span>
                <span v-if="job.skills.length > 5" class="skill-more">
                  +{{ job.skills.length - 5 }} more
                </span>
              </div>

              <div class="job-card__footer">
                <div class="job-card__actions">
                  <button class="btn btn--primary">
                    View Details
                    <i class="fas fa-arrow-right"></i>
                  </button>
                  <button @click.stop="quickApply(job)" class="btn btn--ghost">
                    <i class="fas fa-paper-plane"></i>
                    Quick Apply
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Results State -->
        <div v-else-if="!loading && hasActiveFilters" class="no-results-state">
          <div class="no-results-icon">
            <i class="fas fa-search"></i>
          </div>
          <h3>No Positions Match Your Criteria</h3>
          <p>Try adjusting your filters or search terms to find more opportunities.</p>
          <button @click="clearFilters" class="btn btn--primary">
            <i class="fas fa-times"></i>
            Clear All Filters
          </button>
        </div>

        <!-- Empty State -->
        <div v-else-if="!loading && totalPositions === 0" class="empty-state">
          <div class="empty-icon">
            <i class="fas fa-briefcase"></i>
          </div>
          <h3>No Open Positions</h3>
          <p>We don't have any open positions at the moment, but feel free to send us your resume for future opportunities.</p>
          <router-link to="/contact" class="btn btn--primary">
            Send Your Resume
            <i class="fas fa-envelope"></i>
          </router-link>
        </div>
      </div>
    </section>

    <!-- Enhanced Job Details Modal -->
    <div v-if="selectedJob" class="job-modal" @click="closeModal">
      <div class="job-modal__content" @click.stop>
        <button class="job-modal__close" @click="closeModal">
          <i class="fas fa-times"></i>
        </button>

        <div class="job-modal__header">
          <div class="job-modal__title-section">
            <h2 class="job-modal__title">{{ selectedJob.title }}</h2>
            <div class="job-modal__meta">
              <span class="job-modal__department">{{ selectedJob.department }}</span>
              <span class="job-modal__type">{{ formatEmploymentType(selectedJob.employment_type) }}</span>
              <span class="job-modal__experience">{{ formatExperienceLevel(selectedJob.experience_level) }}</span>
            </div>
            <div class="job-modal__location-info">
              <i class="fas fa-map-marker-alt"></i>
              <span>{{ selectedJob.location }}</span>
              <span v-if="selectedJob.remote_available" class="job-modal__remote">
                <i class="fas fa-wifi"></i>
                Remote Available
              </span>
            </div>
            <div v-if="selectedJob.salary_range" class="job-modal__salary">
              <i class="fas fa-dollar-sign"></i>
              {{ selectedJob.salary_range }}
            </div>
          </div>

          <div class="job-modal__actions">
            <button @click="quickApply(selectedJob)" class="btn btn--primary btn--lg">
              <i class="fas fa-paper-plane"></i>
              Apply Now
            </button>
            <button @click="shareJob(selectedJob)" class="btn btn--ghost">
              <i class="fas fa-share"></i>
              Share
            </button>
          </div>
        </div>

        <div class="job-modal__body">
          <div class="job-modal__tabs">
            <button
              v-for="tab in modalTabs"
              :key="tab.id"
              @click="activeModalTab = tab.id"
              class="modal-tab"
              :class="{ active: activeModalTab === tab.id }"
            >
              <i :class="tab.icon"></i>
              {{ tab.label }}
            </button>
          </div>

          <div class="job-modal__tab-content">
            <!-- Description Tab -->
            <div v-show="activeModalTab === 'description'" class="tab-pane">
              <div class="job-modal__section">
                <h4>About This Role</h4>
                <div class="job-modal__content-text" v-html="formatText(selectedJob.description)"></div>
              </div>
            </div>

            <!-- Requirements Tab -->
            <div v-show="activeModalTab === 'requirements'" class="tab-pane">
              <div class="job-modal__section">
                <h4>What We're Looking For</h4>
                <div class="job-modal__content-text" v-html="formatText(selectedJob.requirements)"></div>
              </div>

              <div v-if="selectedJob.skills && selectedJob.skills.length" class="job-modal__section">
                <h4>Required Skills</h4>
                <div class="job-modal__skills">
                  <span
                    v-for="skill in selectedJob.skills"
                    :key="skill"
                    class="job-modal__skill-tag"
                  >
                    {{ skill }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Benefits Tab -->
            <div v-show="activeModalTab === 'benefits'" class="tab-pane">
              <div class="job-modal__section">
                <h4>What We Offer</h4>
                <div v-if="selectedJob.benefits" class="job-modal__content-text" v-html="formatText(selectedJob.benefits)"></div>
                <div v-else class="job-modal__no-content">
                  <p>Benefits information will be discussed during the interview process.</p>
                </div>
              </div>

              <div class="job-modal__section">
                <h4>Application Details</h4>
                <div class="application-details">
                  <div class="detail-item">
                    <i class="fas fa-calendar"></i>
                    <div>
                      <strong>Application Deadline</strong>
                      <span>{{ selectedJob.application_deadline ? formatDate(selectedJob.application_deadline) : 'Open until filled' }}</span>
                    </div>
                  </div>
                  <div class="detail-item">
                    <i class="fas fa-clock"></i>
                    <div>
                      <strong>Response Time</strong>
                      <span>We typically respond within 1-2 weeks</span>
                    </div>
                  </div>
                  <div class="detail-item">
                    <i class="fas fa-users"></i>
                    <div>
                      <strong>Hiring Manager</strong>
                      <span>{{ selectedJob.department }} Team Lead</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Apply Modal -->
    <div v-if="showQuickApplyModal" class="quick-apply-modal" @click="closeQuickApplyModal">
      <div class="quick-apply-modal__content" @click.stop>
        <div class="quick-apply-modal__header">
          <h3>Quick Apply - {{ quickApplyJob?.title }}</h3>
          <button @click="closeQuickApplyModal" class="btn-close">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="quick-apply-modal__body">
          <p>This will redirect you to our contact page where you can submit your application with all necessary details.</p>
          <div class="quick-apply-actions">
            <router-link
              :to="`/contact?position=${encodeURIComponent(quickApplyJob?.title)}&department=${encodeURIComponent(quickApplyJob?.department)}`"
              class="btn btn--primary"
              @click="closeQuickApplyModal"
            >
              Continue to Application
              <i class="fas fa-arrow-right"></i>
            </router-link>
            <button @click="closeQuickApplyModal" class="btn btn--ghost">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Why Work With Us Section -->
    <section class="career-benefits">
      <div class="container">
        <div class="section-header">
          <h2>Why Work With Us?</h2>
          <p>Discover the benefits of joining our team</p>
        </div>

        <div class="benefits-grid">
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-laptop-code"></i>
            </div>
            <h4>Latest Technology</h4>
            <p>Work with cutting-edge tools and technologies in modern development environments.</p>
          </div>
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-users"></i>
            </div>
            <h4>Great Team</h4>
            <p>Join a supportive team of talented professionals who are passionate about what they do.</p>
          </div>
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <h4>Career Growth</h4>
            <p>Continuous learning opportunities and clear career advancement paths.</p>
          </div>
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-balance-scale"></i>
            </div>
            <h4>Work-Life Balance</h4>
            <p>Flexible working hours and remote work options to maintain a healthy balance.</p>
          </div>
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-heart"></i>
            </div>
            <h4>Health Benefits</h4>
            <p>Comprehensive health insurance and wellness programs for you and your family.</p>
          </div>
          <div class="benefit-card">
            <div class="benefit-icon">
              <i class="fas fa-graduation-cap"></i>
            </div>
            <h4>Learning Budget</h4>
            <p>Annual budget for courses, conferences, and professional development.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="career-cta">
      <div class="container">
        <div class="career-cta__content">
          <h2 class="career-cta__title">Ready to Join Our Team?</h2>
          <p class="career-cta__text">
            Don't see a perfect match? We're always looking for talented people.
            Send us your resume and we'll keep you in mind for future opportunities.
          </p>
          <div class="career-cta__actions">
            <router-link to="/contact" class="btn btn--primary btn--xl">
              Send Your Resume
              <i class="fas fa-upload"></i>
            </router-link>
            <router-link to="/team" class="btn btn--ghost btn--xl">
              Meet Our Team
              <i class="fas fa-users"></i>
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

export default {
  name: 'CareerPage',
  setup() {
    const careers = ref([])
    const selectedJob = ref(null)
    const loading = ref(false)
    const searchQuery = ref('')
    const departmentFilter = ref('')
    const typeFilter = ref('')
    const experienceFilter = ref('')
    const locationFilter = ref('')
    const sortBy = ref('priority')
    const activeModalTab = ref('description')
    const showQuickApplyModal = ref(false)
    const quickApplyJob = ref(null)

    // Организационные данные
    const organizationData = ref({
      departments: [],
      employment_types: [],
      experience_levels: [],
      locations: [],
      skills: []
    })

    const modalTabs = ref([
      { id: 'description', label: 'Description', icon: 'fas fa-file-text' },
      { id: 'requirements', label: 'Requirements', icon: 'fas fa-list-check' },
      { id: 'benefits', label: 'Benefits', icon: 'fas fa-heart' }
    ])

    // Computed properties
    const totalPositions = computed(() => careers.value.length)

    const activePositions = computed(() =>
      careers.value.filter(career => career.is_active).length
    )

    const urgentPositions = computed(() =>
      careers.value.filter(career => career.priority >= 75 && career.is_active).length
    )

    const remotePositions = computed(() =>
      careers.value.filter(career => career.remote_available && career.is_active).length
    )

    const departments = computed(() => {
      // Используем данные из OrganizationData, но также добавляем уникальные департаменты из вакансий
      const orgDepartments = organizationData.value.departments.map(d => d.label)
      const careerDepartments = [...new Set(careers.value.map(career => career.department))]
      return [...new Set([...orgDepartments, ...careerDepartments])].sort()
    })

    const locations = computed(() => {
      // Используем данные из OrganizationData, но также добавляем уникальные локации из вакансий
      const orgLocations = organizationData.value.locations.map(l => l.label)
      const careerLocations = [...new Set(careers.value.map(career => career.location))]
      return [...new Set([...orgLocations, ...careerLocations])].sort()
    })

    const teamSize = computed(() => {
      // Можно получить из API статистики команды
      return 25
    })

    const filteredCareers = computed(() => {
      let filtered = careers.value.filter(career => {
        // Only show active positions on public page
        if (!career.is_active) return false

        // Search filter
        const matchesSearch = !searchQuery.value ||
          career.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          career.department.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          career.location.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          career.short_description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          (career.skills && career.skills.some(skill =>
            skill.toLowerCase().includes(searchQuery.value.toLowerCase())
          ))

        // Department filter
        const matchesDepartment = !departmentFilter.value ||
          career.department === departmentFilter.value

        // Type filter
        const matchesType = !typeFilter.value ||
          career.employment_type === typeFilter.value

        // Experience filter
        const matchesExperience = !experienceFilter.value ||
          career.experience_level === experienceFilter.value

        // Location filter
        const matchesLocation = !locationFilter.value ||
          (locationFilter.value === 'remote' ? career.remote_available : career.location === locationFilter.value)

        return matchesSearch && matchesDepartment && matchesType && matchesExperience && matchesLocation
      })

      // Apply sorting
      return filtered.sort((a, b) => {
        switch (sortBy.value) {
          case 'title':
            return a.title.localeCompare(b.title)
          case 'department':
            return a.department.localeCompare(b.department)
          case 'created_at':
            return new Date(b.created_at) - new Date(a.created_at)
          case 'deadline':
            if (!a.application_deadline && !b.application_deadline) return 0
            if (!a.application_deadline) return 1
            if (!b.application_deadline) return -1
            return new Date(a.application_deadline) - new Date(b.application_deadline)
          case 'priority':
          default:
            const aPriority = a.priority || 0
            const bPriority = b.priority || 0
            if (aPriority !== bPriority) {
              return bPriority - aPriority
            }
            return new Date(b.created_at || 0) - new Date(a.created_at || 0)
        }
      })
    })

    const hasActiveFilters = computed(() => {
      return searchQuery.value || departmentFilter.value || typeFilter.value ||
             experienceFilter.value || locationFilter.value
    })

    // Methods
    const loadOrganizationData = async () => {
      try {
        // Загружаем все организационные данные параллельно
        const [departments, employmentTypes, experienceLevels, locations, skills] = await Promise.all([
          axios.get('/api/organization/departments').catch(() => ({ data: { data: [] } })),
          axios.get('/api/organization/employment-types').catch(() => ({ data: { data: [] } })),
          axios.get('/api/organization/experience-levels').catch(() => ({ data: { data: [] } })),
          axios.get('/api/organization/locations').catch(() => ({ data: { data: [] } })),
          axios.get('/api/organization/skills').catch(() => ({ data: { data: [] } }))
        ])

        organizationData.value = {
          departments: departments.data.data || getDefaultDepartments(),
          employment_types: employmentTypes.data.data || getDefaultEmploymentTypes(),
          experience_levels: experienceLevels.data.data || getDefaultExperienceLevels(),
          locations: locations.data.data || getDefaultLocations(),
          skills: skills.data.data || getDefaultSkills()
        }
      } catch (error) {
        console.error('Failed to load organization data:', error)
        // Используем fallback значения
        organizationData.value = {
          departments: getDefaultDepartments(),
          employment_types: getDefaultEmploymentTypes(),
          experience_levels: getDefaultExperienceLevels(),
          locations: getDefaultLocations(),
          skills: getDefaultSkills()
        }
      }
    }

    const getDefaultDepartments = () => [
      { key: 'engineering', label: 'Engineering' },
      { key: 'design', label: 'Design' },
      { key: 'marketing', label: 'Marketing' },
      { key: 'sales', label: 'Sales' },
      { key: 'hr', label: 'Human Resources' },
      { key: 'finance', label: 'Finance' },
      { key: 'operations', label: 'Operations' },
      { key: 'customer-support', label: 'Customer Support' }
    ]

    const getDefaultEmploymentTypes = () => [
      { key: 'full-time', label: 'Full Time' },
      { key: 'part-time', label: 'Part Time' },
      { key: 'contract', label: 'Contract' },
      { key: 'internship', label: 'Internship' }
    ]

    const getDefaultExperienceLevels = () => [
      { key: 'entry', label: 'Entry Level' },
      { key: 'junior', label: 'Junior' },
      { key: 'mid', label: 'Mid Level' },
      { key: 'senior', label: 'Senior' },
      { key: 'lead', label: 'Lead' },
      { key: 'principal', label: 'Principal' }
    ]

    const getDefaultLocations = () => [
      { key: 'remote', label: 'Remote' },
      { key: 'new-york', label: 'New York, NY' },
      { key: 'san-francisco', label: 'San Francisco, CA' },
      { key: 'london', label: 'London, UK' },
      { key: 'berlin', label: 'Berlin, Germany' }
    ]

    const getDefaultSkills = () => [
      { key: 'javascript', label: 'JavaScript' },
      { key: 'php', label: 'PHP' },
      { key: 'laravel', label: 'Laravel' },
      { key: 'vuejs', label: 'Vue.js' },
      { key: 'react', label: 'React' }
    ]

    const loadCareers = async () => {
      try {
        loading.value = true
        const response = await axios.get('/api/careers')
        careers.value = response.data.data || []
      } catch (error) {
        console.error('Failed to load careers:', error)
        careers.value = []
      } finally {
        loading.value = false
      }
    }

    // Методы для работы с организационными данными
    const getDepartmentLabel = (departmentKey) => {
      const dept = organizationData.value.departments.find(d => d.key === departmentKey || d.label === departmentKey)
      return dept ? dept.label : departmentKey
    }

    const getEmploymentTypeLabel = (typeKey) => {
      const type = organizationData.value.employment_types.find(t => t.key === typeKey || t.label === typeKey)
      return type ? type.label : formatEmploymentType(typeKey)
    }

    const getExperienceLevelLabel = (levelKey) => {
      const level = organizationData.value.experience_levels.find(l => l.key === levelKey || l.label === levelKey)
      return level ? level.label : formatExperienceLevel(levelKey)
    }

    const getLocationLabel = (locationKey) => {
      const location = organizationData.value.locations.find(l => l.key === locationKey || l.label === locationKey)
      return location ? location.label : locationKey
    }

    const getSkillLabel = (skillKey) => {
      const skill = organizationData.value.skills.find(s => s.key === skillKey || s.label === skillKey)
      return skill ? skill.label : skillKey
    }

    // Обновленные методы форматирования с использованием централизованных данных
    const formatEmploymentType = (type) => {
      const orgType = organizationData.value.employment_types.find(t => t.key === type)
      if (orgType) return orgType.label

      // Fallback к старой логике
      const types = {
        'full-time': 'Full Time',
        'part-time': 'Part Time',
        'contract': 'Contract',
        'internship': 'Internship'
      }
      return types[type] || type
    }

    const formatExperienceLevel = (level) => {
      const orgLevel = organizationData.value.experience_levels.find(l => l.key === level)
      if (orgLevel) return orgLevel.label

      // Fallback к старой логике
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

    const formatDate = (dateString) => {
      if (!dateString) return ''
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatText = (text) => {
      if (!text) return ''
      // Convert line breaks to HTML and handle bullet points
      return text
        .replace(/\n/g, '<br>')
        .replace(/•\s/g, '<li>')
        .replace(/^\s*-\s/gm, '<li>')
        .replace(/(<li>.*?)(?=<li>|$)/gs, '<ul>$1</ul>')
        .replace(/<li>/g, '<li>')
        .replace(/<\/ul><ul>/g, '')
    }

    const isDeadlineSoon = (deadline) => {
      if (!deadline) return false
      const now = new Date()
      const deadlineDate = new Date(deadline)
      const diff = deadlineDate - now
      return diff > 0 && diff < 7 * 24 * 60 * 60 * 1000 // 7 days
    }

    const showJobDetails = (job) => {
      selectedJob.value = job
      activeModalTab.value = 'description'
      document.body.style.overflow = 'hidden'
    }

    const closeModal = () => {
      selectedJob.value = null
      document.body.style.overflow = 'auto'
    }

    const quickApply = (job) => {
      quickApplyJob.value = job
      showQuickApplyModal.value = true
    }

    const closeQuickApplyModal = () => {
      showQuickApplyModal.value = false
      quickApplyJob.value = null
    }

    const shareJob = (job) => {
      if (navigator.share) {
        navigator.share({
          title: `${job.title} - ${job.department}`,
          text: job.short_description,
          url: window.location.href
        })
      } else {
        // Fallback to clipboard
        const url = window.location.href
        navigator.clipboard.writeText(url).then(() => {
          alert('Job link copied to clipboard!')
        })
      }
    }

    const applyFilters = () => {
      // Filters are applied automatically via computed property
    }

    const clearFilters = () => {
      searchQuery.value = ''
      departmentFilter.value = ''
      typeFilter.value = ''
      experienceFilter.value = ''
      locationFilter.value = ''
    }

    onMounted(async () => {
      // Загружаем организационные данные и вакансии параллельно
      await Promise.all([
        loadOrganizationData(),
        loadCareers()
      ])

      // Handle escape key for modals
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          if (showQuickApplyModal.value) {
            closeQuickApplyModal()
          } else if (selectedJob.value) {
            closeModal()
          }
        }
      })
    })

    return {
      careers,
      selectedJob,
      loading,
      searchQuery,
      departmentFilter,
      typeFilter,
      experienceFilter,
      locationFilter,
      sortBy,
      activeModalTab,
      showQuickApplyModal,
      quickApplyJob,
      modalTabs,
      organizationData,
      totalPositions,
      activePositions,
      urgentPositions,
      remotePositions,
      departments,
      locations,
      teamSize,
      filteredCareers,
      hasActiveFilters,
      showJobDetails,
      closeModal,
      quickApply,
      closeQuickApplyModal,
      shareJob,
      applyFilters,
      clearFilters,
      formatEmploymentType,
      formatExperienceLevel,
      formatDate,
      formatText,
      isDeadlineSoon,
      // Новые методы для работы с организационными данными
      getDepartmentLabel,
      getEmploymentTypeLabel,
      getExperienceLevelLabel,
      getLocationLabel,
      getSkillLabel
    }
  }
}
</script>

<style lang="scss" scoped>
@use '../../css/pages/career-page';
</style>
